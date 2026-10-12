<?php

namespace App\Http\Controllers;

use App\Events\MatchScoreUpdated;
use App\Models\GameMatch;
use App\Models\PlayerProfile;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use App\Models\User;
use App\Notifications\TournamentApplicationStatusChanged;
use App\Notifications\MatchResultNotification;
use App\Services\RankingService;
use App\Services\TournamentBracketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    protected TournamentBracketService $bracketService;
    protected RankingService $rankingService;

    public function __construct(TournamentBracketService $bracketService, RankingService $rankingService)
    {
        $this->bracketService = $bracketService;
        $this->rankingService = $rankingService;
    }

    public function index(): View
    {
        Gate::authorize('access-dashboard');

        // Live and upcoming matches for the console
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

        // Pending and recent applications
        $pendingApplications = TournamentApplication::where('status', 'pending')
            ->with(['tournament', 'team.manager'])
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedApplications = TournamentApplication::where('status', 'approved')
            ->with(['tournament', 'team.manager'])
            ->take(5)
            ->get();

        // Injured players across rosters
        $injuredPlayers = PlayerProfile::where('is_injured', true)
            ->with(['user.teams'])
            ->get();

        // Tournaments overview
        $tournaments = Tournament::withCount(['teams', 'matches'])->get();

        return view('pages.admin.dashboard', compact(
            'activeMatches',
            'completedMatches',
            'pendingApplications',
            'approvedApplications',
            'injuredPlayers',
            'tournaments'
        ));
    }

    public function updateScore(Request $request, GameMatch $match): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-dashboard');

        $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'status' => 'required|in:upcoming,live,completed',
            'event_type' => 'nullable|string',
            'event_description' => 'nullable|string',
        ]);

        $prevStatus = $match->status;
        $matchData = $match->match_data ?? ['events' => []];

        // Add timeline event if provided
        if ($request->filled('event_description')) {
            $events = $matchData['events'] ?? [];
            $events[] = [
                'time' => now()->format('H:i'),
                'type' => $request->event_type ?? 'score',
                'description' => $request->event_description,
            ];
            $matchData['events'] = $events;
        }

        $match->update([
            'home_score' => $request->home_score,
            'away_score' => $request->away_score,
            'status' => $request->status,
            'match_data' => $matchData,
        ]);

        // Broadcast event
        event(new MatchScoreUpdated(
            $match,
            $request->event_type ?? 'score_update',
            $request->event_description ?? "Score updated to {$match->home_score} - {$match->away_score}"
        ));

        // If completed just now, recalculate rankings and advance bracket if tournament match
        if ($match->status === 'completed' && $prevStatus !== 'completed') {
            $this->rankingService->recalculateTeamRankings();

            if ($match->tournament_id) {
                $this->bracketService->advanceBracketIfCompleted($match->tournament);
            }

            // Notify followers
            foreach ($match->bookmarks as $bookmark) {
                $bookmark->user->notify(new MatchResultNotification($match));
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'match' => $match->fresh(['homeTeam', 'awayTeam']),
                'message' => 'Score updated and broadcasted.'
            ]);
        }

        return back()->with('success', "Score updated for {$match->homeTeam->name} vs {$match->awayTeam->name}!");
    }

    public function reviewApplication(Request $request, TournamentApplication $application): RedirectResponse
    {
        Gate::authorize('access-dashboard');

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $application->update([
            'status' => $request->status,
        ]);

        // Notify team manager
        if ($application->team && $application->team->manager) {
            $application->team->manager->notify(new TournamentApplicationStatusChanged($application));
        }

        return back()->with('success', "Application for team {$application->team->name} was marked as {$request->status}!");
    }

    public function clearInjury(PlayerProfile $profile): RedirectResponse
    {
        Gate::authorize('access-dashboard');

        $profile->update([
            'is_injured' => false,
            'injury_notes' => 'Medically cleared by league authority on ' . now()->toFormattedDateString(),
        ]);

        return back()->with('success', "Player {$profile->user->name} has been medically cleared!");
    }
}

