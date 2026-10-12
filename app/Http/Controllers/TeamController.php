<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Team;
use App\Models\User;
use App\Models\MatchInvitation;
use App\Notifications\MatchInvitationReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $query = Team::with(['manager', 'players']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('home_venue', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sport')) {
            $query->where('sport_type', $request->sport);
        }

        $teams = $query->orderBy('areal_ranking', 'desc')->paginate(12);
        $sports = Team::select('sport_type')->distinct()->pluck('sport_type');

        return view('pages.teams.index', compact('teams', 'sports'));
    }

    public function show(string $slug): View
    {
        $team = Team::where('slug', $slug)
            ->with(['manager', 'players.playerProfile', 'tournaments'])
            ->firstOrFail();

        $matches = GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where(function ($q) use ($team) {
                $q->where('home_team_id', $team->id)
                  ->orWhere('away_team_id', $team->id);
            })
            ->orderBy('scheduled_at', 'desc')
            ->get();

        // Available free-agent or unassigned players if current user is manager of this team
        $availablePlayers = [];
        $otherTeams = [];
        if (Auth::check() && (Auth::id() === $team->manager_id || Auth::user()->isAdmin())) {
            $currentRosterIds = $team->players->pluck('id')->toArray();
            $availablePlayers = User::where('role', 'player')
                ->whereNotIn('id', $currentRosterIds)
                ->with('playerProfile')
                ->get();

            $otherTeams = Team::where('id', '!=', $team->id)
                ->where('sport_type', $team->sport_type)
                ->get();
        }

        return view('pages.teams.show', compact('team', 'matches', 'availablePlayers', 'otherTeams'));
    }

    public function create(): View
    {
        return view('pages.teams.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sport_type' => 'required|string|max:50',
            'home_venue' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team = Team::create([
            'manager_id' => Auth::id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'sport_type' => $request->sport_type,
            'home_venue' => $request->home_venue ?? 'City Sports Arena',
            'description' => $request->description,
            'areal_ranking' => rand(950, 1100),
        ]);

        return redirect()->route('teams.show', $team->slug)
            ->with('success', "Team {$team->name} created successfully! You can now recruit players.");
    }

    public function edit(Team $team): View
    {
        Gate::authorize('manage-team', $team);
        return view('pages.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        Gate::authorize('manage-team', $team);

        $request->validate([
            'name' => 'required|string|max:255',
            'sport_type' => 'required|string|max:50',
            'home_venue' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team->update($request->only(['name', 'sport_type', 'home_venue', 'description']));

        return redirect()->route('teams.show', $team->slug)
            ->with('success', 'Team profile updated successfully.');
    }

    public function addPlayer(Request $request, Team $team): RedirectResponse
    {
        Gate::authorize('manage-team', $team);

        $request->validate([
            'player_id' => 'required|exists:users,id',
            'position' => 'required|string|max:50',
        ]);

        $player = User::findOrFail($request->player_id);

        if ($team->players()->where('user_id', $player->id)->exists()) {
            return back()->with('error', 'Player is already in this team roster.');
        }

        $team->players()->attach($player->id, [
            'position' => $request->position,
            'status' => 'active',
        ]);

        return back()->with('success', "{$player->name} has been enrolled in {$team->name} roster as {$request->position}!");
    }

    public function removePlayer(Team $team, User $player): RedirectResponse
    {
        Gate::authorize('manage-team', $team);
        $team->players()->detach($player->id);
        return back()->with('info', "{$player->name} was removed from the roster.");
    }

    public function proposeFriendly(Request $request, Team $team): RedirectResponse
    {
        Gate::authorize('manage-team', $team);

        $request->validate([
            'opponent_team_id' => 'required|different:team|exists:teams,id',
            'scheduled_at' => 'required|date|after:now',
            'venue' => 'required|string|max:255',
        ]);

        $match = GameMatch::create([
            'tournament_id' => null, // null denotes friendly
            'home_team_id' => $team->id,
            'away_team_id' => $request->opponent_team_id,
            'stage' => 'friendly',
            'scheduled_at' => $request->scheduled_at,
            'venue' => $request->venue,
            'status' => 'upcoming',
            'home_score' => 0,
            'away_score' => 0,
            'match_data' => [
                'type' => 'friendly',
                'proposed_by' => Auth::user()->name,
            ],
        ]);

        // Auto-invite active players from the roster
        foreach ($team->players as $rosterPlayer) {
            $invitation = MatchInvitation::create([
                'match_id' => $match->id,
                'player_id' => $rosterPlayer->id,
                'team_id' => $team->id,
                'status' => 'pending',
                'manager_notes' => 'Callup for upcoming friendly fixture at ' . $match->venue,
            ]);

            $rosterPlayer->notify(new MatchInvitationReceived($invitation));
        }

        return back()->with('success', 'Friendly match proposed and callup notifications broadcast to players!');
    }
}

