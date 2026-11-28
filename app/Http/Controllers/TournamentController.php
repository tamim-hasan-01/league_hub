<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use App\Models\User;
use App\Notifications\TournamentApplicationStatusChanged;
use App\Services\TournamentBracketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
        $canManageTournament = Auth::check() && Gate::allows('manage-tournament', $tournament);
        $invitableTeams = collect();
        $invitableManagers = collect();
        $pendingTournamentApplications = collect();
        $tournamentInvitations = collect();
        $hasTournamentCapacity = false;

        if (Auth::check() && Auth::user()->isManager()) {
            $userTeams = Team::where('manager_id', Auth::id())->get();
        }

        if ($canManageTournament) {
            $activeApplicationTeamIds = $tournament->applications()
                ->whereIn('status', ['pending', 'approved'])
                ->pluck('team_id');
            $activeInvitationQuery = $tournament->invitations()
                ->whereIn('status', ['pending', 'accepted']);
            $activeInvitationTeamIds = (clone $activeInvitationQuery)
                ->whereNotNull('team_id')
                ->pluck('team_id');
            $activeInvitationManagerIds = (clone $activeInvitationQuery)->pluck('manager_id');
            $activeTeamIds = $activeApplicationTeamIds->merge($activeInvitationTeamIds)->unique();
            $activeManagerIds = Team::whereIn('id', $activeTeamIds)
                ->pluck('manager_id')
                ->merge($activeInvitationManagerIds)
                ->unique();
            $hasTournamentCapacity = $tournament->applications()->where('status', 'approved')->count() < $tournament->max_teams;

            if ($tournament->status === 'registration_open' && $hasTournamentCapacity) {
                $invitableTeams = Team::query()
                    ->with('manager')
                    ->where('sport_type', $tournament->sport_type)
                    ->whereNotIn('id', $activeTeamIds)
                    ->whereNotIn('manager_id', $activeManagerIds)
                    ->whereHas('manager', fn ($query) => $query->whereIn('role', ['manager', 'admin']))
                    ->orderBy('name')
                    ->get();

                $invitableManagers = User::query()
                    ->where('role', 'manager')
                    ->whereNotIn('id', $activeManagerIds)
                    ->whereHas('managedTeams', function ($query) use ($tournament, $activeTeamIds) {
                        $query->where('sport_type', $tournament->sport_type)
                            ->whereNotIn('id', $activeTeamIds);
                    })
                    ->orderBy('name')
                    ->get();
            }

            $pendingTournamentApplications = $tournament->applications()
                ->where('status', 'pending')
                ->with('team.manager')
                ->oldest()
                ->get();
            $tournamentInvitations = $tournament->invitations()
                ->with(['manager', 'team'])
                ->latest()
                ->get();
        }

        return view('pages.tournaments.show', compact(
            'tournament',
            'bracketTree',
            'userTeams',
            'canManageTournament',
            'invitableTeams',
            'invitableManagers',
            'pendingTournamentApplications',
            'tournamentInvitations',
            'hasTournamentCapacity',
        ));
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
            'slug' => Str::slug($validated['title']).'-'.Str::random(4),
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

        return back()->with('success', "Application submitted for {$team->name}! Awaiting tournament organizer review.");
    }

    public function inviteTeam(Request $request, Tournament $tournament): RedirectResponse
    {
        Gate::authorize('manage-tournament', $tournament);

        if ($tournament->status !== 'registration_open') {
            return back()->with('error', 'Invitations are only available while registration is open.');
        }

        if ($tournament->applications()->where('status', 'approved')->count() >= $tournament->max_teams) {
            return back()->with('error', 'This tournament has reached its team limit.');
        }

        $validated = $request->validate([
            'team_id' => [
                'required',
                'integer',
                Rule::exists('teams', 'id')->where('sport_type', $tournament->sport_type),
            ],
        ]);
        $team = Team::with('manager')->findOrFail($validated['team_id']);

        if (! $team->manager || (! $team->manager->isManager() && ! $team->manager->isAdmin())) {
            return back()->with('error', 'This team does not have an eligible manager who can respond to invitations.');
        }

        if ($tournament->applications()
            ->where('team_id', $team->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists()) {
            return back()->with('error', 'This team already has an active application for the tournament.');
        }

        $invitation = $tournament->invitations()
            ->where('target_type', 'team')
            ->where('target_id', $team->id)
            ->first();

        if ($invitation && in_array($invitation->status, ['pending', 'accepted'], true)) {
            return back()->with('error', 'This team already has an active invitation.');
        }

        if ($tournament->invitations()
            ->where('manager_id', $team->manager_id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists()) {
            return back()->with('error', 'This manager already has an active invitation for the tournament.');
        }

        $invitationData = [
            'manager_id' => $team->manager_id,
            'team_id' => $team->id,
            'target_type' => 'team',
            'target_id' => $team->id,
            'invited_by' => Auth::id(),
            'status' => 'pending',
        ];

        if ($invitation) {
            $invitation->update($invitationData);
        } else {
            $tournament->invitations()->create($invitationData);
        }

        return back()->with('success', "An invitation was sent to {$team->name}.");
    }

    public function inviteManager(Request $request, Tournament $tournament): RedirectResponse
    {
        Gate::authorize('manage-tournament', $tournament);

        if ($tournament->status !== 'registration_open') {
            return back()->with('error', 'Invitations are only available while registration is open.');
        }

        if ($tournament->applications()->where('status', 'approved')->count() >= $tournament->max_teams) {
            return back()->with('error', 'This tournament has reached its team limit.');
        }

        $validated = $request->validate([
            'manager_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'manager'),
            ],
        ]);
        $manager = User::findOrFail($validated['manager_id']);

        $eligibleTeams = $manager->managedTeams()
            ->where('sport_type', $tournament->sport_type)
            ->get();

        if ($eligibleTeams->isEmpty()) {
            return back()->with('error', 'This manager does not have a team in the tournament sport.');
        }

        if ($tournament->invitations()
            ->where('manager_id', $manager->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists()) {
            return back()->with('error', 'This manager already has an active invitation for the tournament.');
        }

        if ($tournament->applications()
            ->whereIn('status', ['pending', 'approved'])
            ->whereHas('team', fn ($query) => $query->where('manager_id', $manager->id))
            ->exists()) {
            return back()->with('error', 'This manager already has a team with an active tournament application.');
        }

        $invitation = $tournament->invitations()
            ->where('target_type', 'manager')
            ->where('target_id', $manager->id)
            ->first();
        $invitationData = [
            'manager_id' => $manager->id,
            'team_id' => null,
            'target_type' => 'manager',
            'target_id' => $manager->id,
            'invited_by' => Auth::id(),
            'status' => 'pending',
        ];

        if ($invitation) {
            $invitation->update($invitationData);
        } else {
            $tournament->invitations()->create($invitationData);
        }

        return back()->with('success', "An invitation was sent to {$manager->name}.");
    }

    public function reviewApplication(
        Request $request,
        Tournament $tournament,
        TournamentApplication $application,
    ): RedirectResponse {
        Gate::authorize('manage-tournament', $tournament);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);
        $status = $validated['status'];

        $application = DB::transaction(function () use ($tournament, $application, $status) {
            $lockedTournament = Tournament::query()->lockForUpdate()->findOrFail($tournament->id);
            $lockedApplication = $lockedTournament->applications()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $status === 'approved'
                && $lockedApplication->status !== 'approved'
                && $lockedTournament->applications()->where('status', 'approved')->count() >= $lockedTournament->max_teams
            ) {
                return null;
            }

            $lockedApplication->update(['status' => $status]);

            return $lockedApplication->load(['team.manager', 'tournament']);
        });

        if (! $application) {
            return back()->with('error', 'The tournament has reached its team limit.');
        }

        if ($application->team->manager) {
            $application->team->manager->notify(new TournamentApplicationStatusChanged($application));
        }

        return back()->with('success', 'The tournament application was '.$status.'.');
    }

    public function generateBracket(Tournament $tournament): RedirectResponse
    {
        Gate::authorize('create-tournament');

        $result = $this->bracketService->generateBracket($tournament);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', 'Tournament brackets and fixtures generated successfully!');
    }
}
