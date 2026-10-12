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

class MyTournamentsController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $role = $user->role;

        $managerData = [];
        $playerData = [];
        $viewerData = [];

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

        return view('pages.hub.index', compact('user', 'role', 'managerData', 'playerData', 'viewerData'));
    }

    public function respondInvitation(Request $request, MatchInvitation $invitation): RedirectResponse
    {
        $user = Auth::user();
        if ($invitation->player_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:accepted,declined',
        ]);

        $invitation->update(['status' => $request->status]);

        $statusLabel = $request->status === 'accepted' ? 'Accepted' : 'Declined';
        return back()->with('success', "Match call-up has been {$statusLabel}.");
    }

    public function toggleBookmark(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $type = $request->type; // 'match' or 'tournament'
        $id = $request->id;

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

