<?php

namespace App\Http\Controllers;

use App\Events\MatchScoreUpdated;
use App\Models\GameMatch;
use App\Models\PlayerProfile;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use App\Models\User;
use App\Notifications\MatchResultNotification;
use App\Notifications\TournamentApplicationStatusChanged;
use App\Services\RankingService;
use App\Services\TournamentBracketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * AdminDashboardController
 *
 * Provides authority operations for tournament organizers and platform administrators:
 * live scoring console, tournament bracket progression, application reviews, and medical clearances.
 */
class AdminDashboardController extends Controller
{
    protected TournamentBracketService $bracketService;

    protected RankingService $rankingService;

    /**
     * Initialize controller with required bracket and ranking domain services.
     */
    public function __construct(TournamentBracketService $bracketService, RankingService $rankingService)
    {
        $this->bracketService = $bracketService;
        $this->rankingService = $rankingService;
    }

    /**
     * Display the primary administrative control console.
     *
     * Gathers active fixtures, pending tournament registrations,
     * roster injury reports, and tournament summaries.
     */
    public function index(): View
    {
        // Enforce administrative access authorization gate
        Gate::authorize('access-dashboard');

        // ==========================================
        // 1. Live and Upcoming Match Console
        // Fetch matches prioritizing currently live fixtures
        // ==========================================
        $activeMatches = GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->whereIn('status', ['live', 'upcoming'])
            ->orderByRaw("CASE WHEN status = 'live' THEN 1 ELSE 2 END")
            ->orderBy('scheduled_at')
            ->get();

        $completedMatches = GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')
            ->take(5)
            ->get();

        // ==========================================
        // 2. Team Tournament Applications
        // Segregate pending applications for review from approved entries
        // ==========================================
        $pendingApplications = TournamentApplication::where('status', 'pending')
            ->with(['tournament', 'team.manager'])
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedApplications = TournamentApplication::where('status', 'approved')
            ->with(['tournament', 'team.manager'])
            ->take(5)
            ->get();

        // ==========================================
        // 3. Player Medical and Injury Oversight
        // Query players flagged as injured across all team rosters
        // ==========================================
        $injuredPlayers = PlayerProfile::where('is_injured', true)
            ->with(['user.teams'])
            ->get();

        // ==========================================
        // 4. Tournament Competitions Overview
        // Load competitions with associated team and match counts
        // ==========================================
        $tournaments = Tournament::withCount(['teams', 'matches'])->get();

        $managedUsers = User::whereIn('role', ['manager', 'player', 'viewer'])
            ->orderByRaw("CASE WHEN account_status = 'suspended' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('pages.admin.dashboard', compact(
            'activeMatches',
            'completedMatches',
            'pendingApplications',
            'approvedApplications',
            'injuredPlayers',
            'tournaments',
            'managedUsers'
        ));
    }

    /**
     * Update match scoreline, timeline events, and game status.
     *
     * Dispatches real-time broadcast events and triggers ranking recalculation
     * and knockout bracket advancement when a match concludes.
     */
    public function updateScore(Request $request, GameMatch $match): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-dashboard');

        // Validate incoming scoreline data and status parameters
        $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'status' => 'required|in:upcoming,live,completed',
            'event_type' => 'nullable|string',
            'event_description' => 'nullable|string',
        ]);

        $prevStatus = $match->status;
        $matchData = $match->match_data ?? ['events' => []];

        // ==========================================
        // 1. Timeline Event Appending
        // Record timestamped key incidents (e.g. goals, fouls, substitutions)
        // ==========================================
        if ($request->filled('event_description')) {
            $events = $matchData['events'] ?? [];
            $events[] = [
                'time' => now()->format('H:i'),
                'type' => $request->event_type ?? 'score',
                'description' => $request->event_description,
            ];
            $matchData['events'] = $events;
        }

        // ==========================================
        // 2. Persist Updated Match State
        // ==========================================
        $match->update([
            'home_score' => $request->home_score,
            'away_score' => $request->away_score,
            'status' => $request->status,
            'match_data' => $matchData,
        ]);

        // ==========================================
        // 3. Broadcast Real-Time Update Event
        // ==========================================
        event(new MatchScoreUpdated(
            $match,
            $request->event_type ?? 'score_update',
            $request->event_description ?? "Score updated to {$match->home_score} - {$match->away_score}"
        ));

        // ==========================================
        // 4. Completion Lifecycle Hooks
        // Recalculate team rankings, advance bracket stage, and alert bookmarked users
        // ==========================================
        if ($match->status === 'completed' && $prevStatus !== 'completed') {
            $this->rankingService->recalculateTeamRankings();
            $this->decrementSuspensionsForMatch($match);

            if ($match->tournament_id) {
                $this->bracketService->advanceBracketIfCompleted($match->tournament);
            }

            // Notify followers who bookmarked this match
            foreach ($match->bookmarks as $bookmark) {
                $bookmark->user->notify(new MatchResultNotification($match));
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'match' => $match->fresh(['homeTeam', 'awayTeam']),
                'message' => 'Score updated and broadcasted.',
            ]);
        }

        return back()->with('success', "Score updated for {$match->homeTeam->name} vs {$match->awayTeam->name}!");
    }

    /**
     * Review and decide on a team's tournament entry application.
     *
     * Marks status as approved or rejected and sends notification to the team manager.
     */
    public function reviewApplication(Request $request, TournamentApplication $application): RedirectResponse
    {
        Gate::authorize('access-dashboard');

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $application->update([
            'status' => $request->status,
        ]);

        // Notify team manager of application decision
        if ($application->team && $application->team->manager) {
            $application->team->manager->notify(new TournamentApplicationStatusChanged($application));
        }

        return back()->with('success', "Application for team {$application->team->name} was marked as {$request->status}!");
    }

    /**
     * Medically clear an injured player profile.
     *
     * Resets the injury status to active and appends a clearance log entry.
     */
    public function clearInjury(PlayerProfile $profile): RedirectResponse
    {
        Gate::authorize('access-dashboard');

        $profile->update([
            'is_injured' => false,
            'injury_notes' => 'Medically cleared by league authority on '.now()->toFormattedDateString(),
        ]);

        return back()->with('success', "Player {$profile->user->name} has been medically cleared!");
    }

    private function decrementSuspensionsForMatch(GameMatch $match): void
    {
        $teamIds = array_filter([
            $match->home_team_id,
            $match->away_team_id,
        ]);

        $userIds = collect($teamIds)
            ->flatMap(function (int $teamId) {
                $team = Team::with(['manager', 'players'])->find($teamId);

                if (! $team) {
                    return [];
                }

                $ids = $team->players->pluck('id')->all();
                if ($team->manager_id) {
                    $ids[] = $team->manager_id;
                }

                return $ids;
            })
            ->unique()
            ->values();

        foreach (User::whereIn('id', $userIds)->where('account_status', 'suspended')->get() as $user) {
            $remaining = max(0, (int) $user->suspension_matches_remaining - 1);
            $user->update([
                'account_status' => $remaining > 0 ? 'suspended' : 'active',
                'suspension_matches_remaining' => $remaining,
                'suspension_reason' => $remaining > 0 ? $user->suspension_reason : null,
                'suspended_at' => $remaining > 0 ? $user->suspended_at : null,
                'suspended_by' => $remaining > 0 ? $user->suspended_by : null,
            ]);
        }
    }
}
