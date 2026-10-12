<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentApplication;
use App\Models\Team;
use App\Services\TournamentBracketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TournamentController extends Controller
{
    protected TournamentBracketService $bracketService;

    public function __construct(TournamentBracketService $bracketService)
    {
        $this->bracketService = $bracketService;
    }

    public function index(Request $request): View
    {
        $query = Tournament::withCount(['teams', 'matches']);

        if ($request->filled('sport')) {
            $query->where('sport_type', $request->sport);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tournaments = $query->orderBy('created_at', 'desc')->paginate(9);

        $activeCount = Tournament::whereIn('status', ['registration_open', 'in_progress'])->count();
        $sports = Tournament::select('sport_type')->distinct()->pluck('sport_type');

        return view('pages.tournaments.index', compact('tournaments', 'activeCount', 'sports'));
    }

    public function show(string $slug): View
    {
        $tournament = Tournament::where('slug', $slug)
            ->with(['teams.manager', 'matches.homeTeam', 'matches.awayTeam', 'creator'])
            ->firstOrFail();

        $bracketTree = $this->bracketService->getBracketTree($tournament);
        $userTeams = [];

        if (Auth::check() && Auth::user()->isManager()) {
            $userTeams = Team::where('manager_id', Auth::id())->get();
        }

        return view('pages.tournaments.show', compact('tournament', 'bracketTree', 'userTeams'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create-tournament');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sport_type' => 'required|string',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'prize_pool' => 'nullable|string|max:100',
            'max_teams' => 'required|integer|min:2|max:32',
        ]);

        $tournament = Tournament::create([
            ...$validated,
            'creator_id' => Auth::id(),
            'slug' => Str::slug($validated['title']) . '-' . Str::random(4),
            'status' => 'registration_open',
            'current_stage' => 'group',
        ]);

        return redirect()->route('tournaments.show', $tournament->slug)
            ->with('success', 'Tournament created successfully! Teams can now register.');
    }

    public function apply(Request $request, Tournament $tournament): RedirectResponse
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $team = Team::findOrFail($request->team_id);
        Gate::authorize('apply-tournament', $team);

        // Check if already applied
        $existing = TournamentApplication::where('tournament_id', $tournament->id)
            ->where('team_id', $team->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Team has already applied for this tournament.');
        }

        TournamentApplication::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return back()->with('success', "Application submitted for {$team->name}! Awaiting league admin review.");
    }

    public function generateBracket(Tournament $tournament): RedirectResponse
    {
        Gate::authorize('create-tournament');

        $result = $this->bracketService->generateBracket($tournament);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', 'Tournament brackets and fixtures generated successfully!');
    }
}

