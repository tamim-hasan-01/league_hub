<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\GameMatch;
use App\Models\MatchInvitation;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use App\Models\TournamentInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * MyTournamentsController
 *
 * Manages the personalized user hub, tailoring data according to user roles:
 * - Managers: team rosters, active tournaments, pending applications, friendly matches
 * - Players: match call-up invitations, active rosters, fixture schedule
 * - Viewers & Spectators: bookmarked matches and followed tournaments
 */
class MyTournamentsController extends Controller
{
    /**
     * Display the personalized hub tailored to the authenticated user's role.
     */
    public function index(): View
    {
        $user = Auth::user();
        $role = $user->role;

        $managerData = [];
        $playerData = [];
        $viewerData = [];
        $receivedTournamentInvitations = TournamentInvitation::query()
            ->where('manager_id', $user->id)
            ->where('status', 'pending')
            ->with(['tournament', 'team'])
            ->latest()
            ->get();
        $receivedTeamJoinRequests = collect();
        $myTeamJoinRequests = collect();

        // ==========================================
        // 1. Manager Role Data Assembly
        // Collect managed teams, active tournament participation, and pending applications
        // ==========================================
        if ($user->isManager() || $user->isAdmin()) {
            $teams = Team::where('manager_id', $user->id)->pluck('id');
            $joinRequestQuery = TeamJoinRequest::query()
                ->where('status', 'pending')
                ->with(['team', 'player'])
                ->latest();

            if (! $user->isAdmin()) {
                $joinRequestQuery->whereHas('team', fn ($query) => $query->where('manager_id', $user->id));
            }

            $receivedTeamJoinRequests = $joinRequestQuery->get();
            $managerData = [
                'myTeams' => Team::where('manager_id', $user->id)->with('players')->get(),
                'activeTournaments' => Tournament::whereHas('applications', function ($q) use ($teams) {
                    $q->whereIn('team_id', $teams)->where('status', 'approved');
                })->with(['matches'])->get(),
                'pendingApplications' => TournamentApplication::whereIn('team_id', $teams)
                    ->with(['tournament', 'team'])
                    ->get(),
                'friendlyMatches' => GameMatch::where(function ($q) use ($teams) {
                    $q->whereIn('home_team_id', $teams)->orWhereIn('away_team_id', $teams);
                })->where('stage', 'friendly')->with(['homeTeam', 'awayTeam'])->get(),
            ];
        }

        $memberDirectory = collect();
        if ($user->isAdmin()) {
            $memberDirectory = User::whereIn('role', ['manager', 'player', 'viewer'])
                ->orderByRaw("CASE WHEN account_status = 'suspended' THEN 0 ELSE 1 END")
                ->orderBy('name')
                ->get();
        } elseif ($user->isManager()) {
            $memberDirectory = User::whereIn('role', ['player', 'viewer'])
                ->orderByRaw("CASE WHEN account_status = 'suspended' THEN 0 ELSE 1 END")
                ->orderBy('name')
                ->get();
        }

        // ==========================================
        // 2. Player Role Data Assembly
        // Retrieve incoming match call-ups, confirmed fixtures, and current team rosters
        // ==========================================
        if ($user->isPlayer() || $user->isAdmin()) {
            if ($user->isPlayer()) {
                $myTeamJoinRequests = TeamJoinRequest::where('player_id', $user->id)
                    ->with('team')
                    ->latest()
                    ->get();
            }

            $playerData = [
                'pendingInvitations' => MatchInvitation::where('player_id', $user->id)
                    ->where('status', 'pending')
                    ->with(['match.homeTeam', 'match.awayTeam', 'match.tournament', 'team'])
                    ->get(),
                'acceptedMatches' => MatchInvitation::where('player_id', $user->id)
                    ->where('status', 'accepted')
                    ->with(['match.homeTeam', 'match.awayTeam', 'match.tournament', 'team'])
                    ->get(),
                'declinedMatches' => MatchInvitation::where('player_id', $user->id)
                    ->where('status', 'declined')
                    ->with(['match.homeTeam', 'match.awayTeam', 'team'])
                    ->get(),
                'rosters' => $user->teams()->with('manager')->get(),
            ];
        }

        // ==========================================
        // 3. Viewer & General Follower Bookmarks
        // Load followed matches and tracked tournaments
        // ==========================================
        if ($user->isViewer() || $user->isAdmin() || $user->isManager() || $user->isPlayer()) {
            $viewerData = [
                'bookmarkedMatches' => Bookmark::where('user_id', $user->id)
                    ->whereNotNull('game_match_id')
                    ->with(['match.homeTeam', 'match.awayTeam', 'match.tournament'])
                    ->get(),
                'bookmarkedTournaments' => Bookmark::where('user_id', $user->id)
                    ->whereNotNull('tournament_id')
                    ->with(['tournament.teams'])
                    ->get(),
            ];
        }

        // ==========================================
        // 4. View Rendering
        // Render unified hub view with populated role-specific collections
        // ==========================================
        return view('pages.hub.index', compact(
            'user',
            'role',
            'managerData',
            'playerData',
            'viewerData',
            'memberDirectory',
            'receivedTournamentInvitations',
            'receivedTeamJoinRequests',
            'myTeamJoinRequests',
        ));
    }

    /**
     * Respond to a match call-up invitation as a player.
     *
     * Validates player identity and sets invitation status to accepted or declined.
     */
    public function respondInvitation(Request $request, MatchInvitation $invitation): RedirectResponse
    {
        $user = Auth::user();
        if ($invitation->player_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:accepted,declined',
        ]);

        $invitation->update(['status' => $request->status]);

        $statusLabel = $request->status === 'accepted' ? 'Accepted' : 'Declined';

        return back()->with('success', "Match call-up has been {$statusLabel}.");
    }

    public function respondTournamentInvitation(Request $request, TournamentInvitation $tournamentInvitation): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($tournamentInvitation->manager_id === $user->id, 403);
        $tournamentInvitation->loadMissing(['tournament', 'team']);

        if ($tournamentInvitation->target_type === 'manager') {
            abort_unless($user->isManager(), 403);
        } elseif ($tournamentInvitation->target_type === 'team') {
            abort_unless(
                $tournamentInvitation->team
                && (int) $tournamentInvitation->team->id === (int) $tournamentInvitation->target_id,
                403,
            );
            Gate::authorize('manage-team', $tournamentInvitation->team);
        } else {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['accepted', 'declined'])],
        ]);
        $teamId = null;

        if ($validated['status'] === 'accepted' && $tournamentInvitation->target_type === 'manager') {
            $teamData = $request->validate([
                'team_id' => [
                    'required',
                    'integer',
                    Rule::exists('teams', 'id')
                        ->where('manager_id', $user->id)
                        ->where('sport_type', $tournamentInvitation->tournament->sport_type),
                ],
            ]);
            $teamId = $teamData['team_id'];
        } elseif ($tournamentInvitation->target_type === 'team') {
            $teamId = $tournamentInvitation->team_id;
        }

        $result = DB::transaction(function () use ($tournamentInvitation, $teamId, $validated, $user) {
            $invitation = TournamentInvitation::query()
                ->lockForUpdate()
                ->findOrFail($tournamentInvitation->id);

            if ($invitation->status !== 'pending') {
                return 'not_pending';
            }

            if ($validated['status'] === 'declined') {
                $invitation->update(['status' => 'declined']);

                return 'declined';
            }

            $tournament = Tournament::query()->lockForUpdate()->findOrFail($invitation->tournament_id);
            if ($tournament->status !== 'registration_open') {
                return 'closed';
            }

            $team = Team::query()->lockForUpdate()->findOrFail($teamId);
            if (
                (int) $team->manager_id !== $user->id
                || $team->sport_type !== $tournament->sport_type
                || ($invitation->target_type === 'team' && (int) $team->id !== (int) $invitation->target_id)
            ) {
                return 'invalid_team';
            }

            $application = TournamentApplication::query()
                ->where('tournament_id', $tournament->id)
                ->where('team_id', $team->id)
                ->lockForUpdate()
                ->first();

            if ($application?->status === 'approved') {
                return 'already_joined';
            }

            if (
                $tournament->applications()->where('status', 'approved')->count() >= $tournament->max_teams
            ) {
                return 'full';
            }

            if ($application) {
                $application->update(['status' => 'approved']);
            } else {
                $application = TournamentApplication::create([
                    'tournament_id' => $tournament->id,
                    'team_id' => $team->id,
                    'status' => 'approved',
                ]);
            }

            $invitation->update([
                'status' => 'accepted',
                'team_id' => $team->id,
            ]);

            return 'accepted';
        });

        return match ($result) {
            'accepted' => back()->with('success', 'Tournament invitation accepted. Your team is registered.'),
            'declined' => back()->with('success', 'Tournament invitation declined.'),
            'closed' => back()->with('error', 'This tournament is no longer accepting teams.'),
            'full' => back()->with('error', 'This tournament has reached its team limit.'),
            'already_joined' => back()->with('error', 'This team is already registered for the tournament.'),
            'invalid_team' => back()->with('error', 'Select a team you manage in the tournament sport.'),
            default => back()->with('error', 'This invitation has already been answered.'),
        };
    }

    public function respondTeamJoinRequest(Request $request, TeamJoinRequest $teamJoinRequest): RedirectResponse
    {
        Gate::authorize('manage-team', $teamJoinRequest->team);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $result = DB::transaction(function () use ($teamJoinRequest, $validated) {
            $joinRequest = TeamJoinRequest::query()
                ->lockForUpdate()
                ->findOrFail($teamJoinRequest->id);

            if ($joinRequest->status !== 'pending') {
                return 'not_pending';
            }

            $team = Team::query()->lockForUpdate()->findOrFail($joinRequest->team_id);
            if ($validated['status'] === 'approved' && ! $team->players()->whereKey($joinRequest->player_id)->exists()) {
                $team->players()->attach($joinRequest->player_id, [
                    'position' => $joinRequest->position,
                    'status' => 'active',
                ]);
            }

            $joinRequest->update([
                'status' => $validated['status'],
                'responded_at' => now(),
            ]);

            return $validated['status'];
        });

        if ($result === 'not_pending') {
            return back()->with('error', 'This team request has already been answered.');
        }

        $statusLabel = $result === 'approved' ? 'approved' : 'declined';

        return back()->with('success', "The player request was {$statusLabel}.");
    }

    /**
     * Toggle bookmark state for a match or tournament.
     *
     * Creates a new bookmark record or removes an existing one for the user.
     */
    public function toggleBookmark(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $type = $request->type; // 'match' or 'tournament'
        $id = $request->id;

        // Handle match bookmarking
        if ($type === 'match') {
            $match = GameMatch::findOrFail($id);
            $bookmark = Bookmark::where('user_id', $user->id)->where('game_match_id', $match->id)->first();
            if ($bookmark) {
                $bookmark->delete();

                return back()->with('info', 'Match removed from bookmarks.');
            } else {
                Bookmark::create(['user_id' => $user->id, 'game_match_id' => $match->id]);

                return back()->with('success', 'Match added to your bookmarks!');
            }
            // Handle tournament bookmarking
        } elseif ($type === 'tournament') {
            $tournament = Tournament::findOrFail($id);
            $bookmark = Bookmark::where('user_id', $user->id)->where('tournament_id', $tournament->id)->first();
            if ($bookmark) {
                $bookmark->delete();

                return back()->with('info', 'Tournament unfollowed.');
            } else {
                Bookmark::create(['user_id' => $user->id, 'tournament_id' => $tournament->id]);

                return back()->with('success', 'Tournament followed! You will receive updates.');
            }
        }

        return back();
    }
}
