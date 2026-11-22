<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\GameMatch;
use App\Models\MatchInvitation;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // ==========================================
        // 1. Manager Role Data Assembly
        // Collect managed teams, active tournament participation, and pending applications
        // ==========================================
        if ($user->isManager() || $user->isAdmin()) {
            $teams = Team::where('manager_id', $user->id)->pluck('id');
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
        return view('pages.hub.index', compact('user', 'role', 'managerData', 'playerData', 'viewerData', 'memberDirectory'));
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
