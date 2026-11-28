<x-app-layout>
    <div class="space-y-8" x-data="{ activeTab: 'bracket' }">
        {{-- ========================================== --}}
        {{-- Section: Tournament Hero Banner & Metadata --}}
        {{-- Displays title, sport category, prize pool, team counts, and current stage --}}
        {{-- ========================================== --}}
        <section class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-slate-950 text-white">
            <div class="absolute right-0 top-0 h-full w-2/5 bg-[radial-gradient(circle_at_center,rgba(129,140,248,0.24),transparent_68%)]"></div>
            <div class="relative grid gap-8 px-6 py-8 sm:px-8 lg:grid-cols-[minmax(0,1fr)_280px] lg:px-10 lg:py-10">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full border border-indigo-300/20 bg-indigo-300/10 px-3 py-1 text-xs font-bold text-indigo-200">
                            {{ $tournament->sport_type }}
                        </span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-bold capitalize text-slate-200">
                            {{ str_replace('_', ' ', $tournament->status) }}
                        </span>
                    </div>

                    <p class="mt-5 text-xs font-bold uppercase tracking-[0.16em] text-indigo-300">Tournament overview</p>
                    <h1 class="mt-2 max-w-4xl text-3xl font-black leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                        {{ $tournament->title }}
                    </h1>
                    <p class="mt-4 max-w-3xl text-base leading-7 text-slate-300">
                        {{ $tournament->description ?: 'A registered multi-stage competition managed through LeagueHub.' }}
                    </p>

                    {{-- Competition Fast-Fact Stats Grid --}}
                    <div class="mt-7 grid gap-px overflow-hidden rounded-xl border border-white/10 bg-white/10 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="bg-slate-950/75 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Venue</p>
                            <p class="mt-2 line-clamp-2 text-sm font-bold leading-5 text-white">{{ $tournament->venue ?: 'Venue TBA' }}</p>
                        </div>
                        <div class="bg-slate-950/75 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Prize pool</p>
                            <p class="mt-2 text-sm font-bold text-white">{{ $tournament->prize_pool ?: 'Prize TBA' }}</p>
                        </div>
                        <div class="bg-slate-950/75 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Teams</p>
                            <p class="mt-2 text-sm font-bold text-white">{{ $tournament->teams->count() }} / {{ $tournament->max_teams }}</p>
                        </div>
                        <div class="bg-slate-950/75 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Current phase</p>
                            <p class="mt-2 text-sm font-bold capitalize text-white">{{ str_replace('_', ' ', $tournament->current_stage) }}</p>
                        </div>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- Section: Action Controls (Follow, Apply, Bracket Generation) --}}
                {{-- ========================================== --}}
                <aside class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 lg:self-start">
                    @auth
                        <form action="{{ route('bookmarks.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="tournament">
                            <input type="hidden" name="id" value="{{ $tournament->id }}">
                            @php
                                $isFollowed = auth()->user()->bookmarks()->where('tournament_id', $tournament->id)->exists();
                            @endphp
                            <button type="submit"
                                    class="flex h-12 w-full items-center justify-center gap-2 rounded-xl border text-sm font-bold transition {{ $isFollowed ? 'border-indigo-300/30 bg-indigo-300/15 text-indigo-100' : 'border-white/15 bg-white/5 text-white hover:border-white/30 hover:bg-white/10' }}">
                                <svg class="h-4 w-4 {{ $isFollowed ? 'fill-indigo-200' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                                {{ $isFollowed ? 'Following' : 'Follow tournament' }}
                            </button>
                        </form>

                        @if(auth()->user()->isManager() && $tournament->status === 'registration_open')
                            <button type="button" data-modal-target="apply-team-modal" data-modal-toggle="apply-team-modal"
                                    class="flex h-12 w-full items-center justify-center rounded-xl bg-indigo-500 text-sm font-bold text-white shadow-sm hover:bg-indigo-400">
                                Apply a team
                            </button>
                        @endif

                        @can('create-tournament')
                            <form action="{{ route('tournaments.generateBracket', $tournament->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="flex h-12 w-full items-center justify-center rounded-xl border border-indigo-300/30 bg-indigo-300/10 text-sm font-bold text-indigo-100 hover:bg-indigo-300/20">
                                    Generate brackets
                                </button>
                            </form>
                        @endcan
                    @else
                        <a href="{{ route('login') }}" class="flex h-12 w-full items-center justify-center rounded-xl bg-white text-sm font-bold text-slate-950 hover:bg-indigo-100">
                            Sign in to follow
                        </a>
                    @endauth
                </aside>
            </div>
        </section>

        @if($canManageTournament)
            <section id="tournament-invitations" class="space-y-5 rounded-2xl border border-indigo-200 bg-indigo-50/60 p-5 sm:p-7">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.12em] text-indigo-600">Organizer tools</p>
                    <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Invite teams or managers</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                        Invite a specific team, or invite a manager to choose one of their teams. Pending tournament applications can also be reviewed here.
                    </p>
                </div>

                @if($tournament->status === 'registration_open' && $hasTournamentCapacity)
                    <div class="grid gap-4 lg:grid-cols-2">
                        <form action="{{ route('tournaments.inviteTeam', $tournament) }}" method="POST" class="space-y-3 rounded-xl border border-slate-200 bg-white p-4">
                            @csrf
                            <div>
                                <label for="invite_team_id" class="block text-sm font-bold text-slate-800">Invite a team</label>
                                <p class="mt-1 text-xs text-slate-500">The team manager will accept or decline from My Hub.</p>
                            </div>
                            <select name="team_id" id="invite_team_id" required class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-900">
                                <option value="" selected disabled>Select an eligible team</option>
                                @foreach($invitableTeams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }} — {{ $team->manager->name }}</option>
                                @endforeach
                            </select>
                            @if($invitableTeams->isEmpty())
                                <p class="text-xs text-slate-500">No eligible teams in this sport are available to invite.</p>
                            @endif
                            <button type="submit" @disabled($invitableTeams->isEmpty()) class="h-10 rounded-lg bg-slate-950 px-4 text-sm font-bold text-white hover:bg-indigo-600 disabled:cursor-not-allowed disabled:opacity-50">
                                Send team invitation
                            </button>
                        </form>

                        <form action="{{ route('tournaments.inviteManager', $tournament) }}" method="POST" class="space-y-3 rounded-xl border border-slate-200 bg-white p-4">
                            @csrf
                            <div>
                                <label for="invite_manager_id" class="block text-sm font-bold text-slate-800">Invite a manager</label>
                                <p class="mt-1 text-xs text-slate-500">They will select one of their teams in {{ $tournament->sport_type }} when accepting.</p>
                            </div>
                            <select name="manager_id" id="invite_manager_id" required class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-900">
                                <option value="" selected disabled>Select an eligible manager</option>
                                @foreach($invitableManagers as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @endforeach
                            </select>
                            @if($invitableManagers->isEmpty())
                                <p class="text-xs text-slate-500">No eligible managers with available teams are available to invite.</p>
                            @endif
                            <button type="submit" @disabled($invitableManagers->isEmpty()) class="h-10 rounded-lg bg-slate-950 px-4 text-sm font-bold text-white hover:bg-indigo-600 disabled:cursor-not-allowed disabled:opacity-50">
                                Send manager invitation
                            </button>
                        </form>
                    </div>
                @elseif($tournament->status !== 'registration_open')
                    <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                        Invitations are available only while tournament registration is open.
                    </p>
                @else
                    <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                        This tournament has reached its team limit.
                    </p>
                @endif

                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <h3 class="font-black text-slate-900">Tournament applications to review</h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($pendingTournamentApplications as $application)
                                <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $application->team->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Manager: {{ $application->team->manager->name ?? 'Unknown' }}</p>
                                        @if($application->notes)
                                            <p class="mt-1 text-xs text-slate-500">{{ $application->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('tournaments.applications.review', [$tournament, $application]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="h-9 rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700">Approve</button>
                                        </form>
                                        <form action="{{ route('tournaments.applications.review', [$tournament, $application]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="h-9 rounded-lg border border-rose-200 bg-rose-50 px-3 text-xs font-bold text-rose-700 hover:bg-rose-100">Decline</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="p-4 text-sm text-slate-500">There are no tournament applications waiting for review.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <h3 class="font-black text-slate-900">Invitation activity</h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($tournamentInvitations as $invitation)
                                <div class="flex items-center justify-between gap-3 p-4">
                                    <div>
                                        <p class="font-bold text-slate-900">
                                            @if($invitation->target_type === 'team')
                                                {{ $invitation->team->name ?? 'Team' }}
                                            @else
                                                {{ $invitation->manager->name ?? 'Manager' }}
                                            @endif
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">{{ ucfirst($invitation->target_type) }} invitation</p>
                                    </div>
                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold capitalize text-slate-700">
                                        {{ $invitation->status }}
                                    </span>
                                </div>
                            @empty
                                <p class="p-4 text-sm text-slate-500">No invitations have been sent yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ========================================== --}}
        {{-- Section: Navigation Tabs (Bracket, Teams, Fixtures) --}}
        {{-- ========================================== --}}
        <nav class="overflow-x-auto border-b border-slate-200" aria-label="Tournament sections">
            <div class="flex min-w-max gap-1 sm:gap-2">
                <button type="button" @click="activeTab = 'bracket'"
                        :class="activeTab === 'bracket' ? 'border-slate-950 text-slate-950' : 'border-transparent text-slate-500 hover:text-slate-900'"
                        class="border-b-2 px-4 py-3 text-sm font-bold transition">
                    Bracket
                </button>
                <button type="button" @click="activeTab = 'teams'"
                        :class="activeTab === 'teams' ? 'border-slate-950 text-slate-950' : 'border-transparent text-slate-500 hover:text-slate-900'"
                        class="border-b-2 px-4 py-3 text-sm font-bold transition">
                    Teams <span class="ml-1 text-slate-400">{{ $tournament->teams->count() }}</span>
                </button>
                <button type="button" @click="activeTab = 'fixtures'"
                        :class="activeTab === 'fixtures' ? 'border-slate-950 text-slate-950' : 'border-transparent text-slate-500 hover:text-slate-900'"
                        class="border-b-2 px-4 py-3 text-sm font-bold transition">
                    Fixtures <span class="ml-1 text-slate-400">{{ $tournament->matches->count() }}</span>
                </button>
            </div>
        </nav>

        {{-- ========================================== --}}
        {{-- Section: Knockout Bracket Visual Board     --}}
        {{-- Shows tree progression through quarter, semi, and grand final rounds --}}
        {{-- ========================================== --}}
        <div x-show="activeTab === 'bracket'" class="space-y-5">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Tournament bracket</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Stage progression</h2>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-6" aria-label="Tournament knockout bracket">
                <div class="grid min-w-[820px] grid-cols-3 items-start gap-6">
                    @foreach([
                        'quarter_final' => ['Quarterfinal', 'Quarterfinal fixtures'],
                        'semi_final' => ['Semifinal', 'Semifinal fixtures'],
                        'final' => ['Grand final', 'Championship match'],
                    ] as $stage => [$stageLabel, $emptyLabel])
                        <section class="flex h-full flex-col space-y-4">
                            <div class="rounded-xl border {{ $stage === 'final' ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-white' }} px-4 py-3 text-center">
                                <p class="text-xs font-black uppercase tracking-[0.14em] {{ $stage === 'final' ? 'text-amber-700' : 'text-slate-600' }}">{{ $stageLabel }}</p>
                            </div>
                            @forelse($bracketTree[$stage] ?? [] as $match)
                                <article class="rounded-2xl border {{ $stage === 'final' ? 'border-amber-200 ring-4 ring-amber-50' : 'border-slate-200' }} bg-white p-4 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 text-xs">
                                        <span class="font-bold text-slate-400">{{ $stageLabel }} {{ $loop->iteration }}</span>
                                        <span class="font-semibold text-indigo-600">{{ $match['scheduled_at'] }}</span>
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        @foreach(['home_team', 'away_team'] as $side)
                                            @php $team = $match[$side]; @endphp
                                            <div class="grid grid-cols-[minmax(0,1fr)_44px] items-center gap-3 rounded-xl px-3 py-2.5 {{ $match['winner_id'] && $team && $match['winner_id'] === $team['id'] ? 'bg-indigo-50' : 'bg-slate-50' }}">
                                                <span class="truncate text-sm font-bold text-slate-900">{{ $team['name'] ?? 'TBD' }}</span>
                                                <span class="text-right text-lg font-black tabular-nums {{ $match['winner_id'] && $team && $match['winner_id'] === $team['id'] ? 'text-indigo-600' : 'text-slate-700' }}">
                                                    {{ $team['score'] ?? 0 }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </article>
                            @empty
                                <div class="flex min-h-44 flex-1 flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-8 text-center">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                    <p class="mt-3 text-sm font-bold text-slate-600">{{ $emptyLabel }}</p>
                                    <p class="mt-1 max-w-48 text-xs leading-5 text-slate-400">Waiting for the previous round to finish.</p>
                                </div>
                            @endforelse
                        </section>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- Section: Confirmed Participating Teams     --}}
        {{-- ========================================== --}}
        <div x-show="activeTab === 'teams'" x-cloak class="space-y-5">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Confirmed participants</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Approved team rosters</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($tournament->teams as $team)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)] transition hover:-translate-y-0.5 hover:border-indigo-200">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">{{ $team->sport_type }}</p>
                                <h3 class="mt-2 truncate text-lg font-black text-slate-950">
                                    <a href="{{ route('teams.show', $team->slug) }}" class="hover:text-indigo-600">{{ $team->name }}</a>
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $team->manager->name ?? 'League staff' }}</p>
                            </div>
                            <span class="shrink-0 rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">#{{ $team->areal_ranking }}</span>
                        </div>
                        <a href="{{ route('teams.show', $team->slug) }}" class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-sm font-bold text-indigo-600">
                            View team <span>&rarr;</span>
                        </a>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                        <p class="text-base font-bold text-slate-700">No teams confirmed yet</p>
                        <p class="mt-1 text-sm text-slate-500">Approved registrations will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- Section: Tournament Fixtures and Schedule  --}}
        {{-- ========================================== --}}
        <div x-show="activeTab === 'fixtures'" x-cloak class="space-y-5">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Match schedule</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Fixtures and results</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($tournament->matches as $match)
                    <x-match-card :match="$match" />
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center">
                        <p class="text-base font-bold text-slate-700">No matches scheduled</p>
                        <p class="mt-1 text-sm text-slate-500">Fixtures appear after bracket generation.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- Section: Manager Team Application Modal    --}}
    {{-- ========================================== --}}
    @if(auth()->check() && auth()->user()->isManager())
        <div id="apply-team-modal" tabindex="-1" aria-hidden="true"
             class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-x-hidden overflow-y-auto md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-indigo-600">Team application</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">{{ $tournament->title }}</h3>
                        </div>
                        <button type="button" data-modal-hide="apply-team-modal" aria-label="Close team application" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">
                            <span class="text-lg">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('tournaments.apply', $tournament->id) }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="team_id" class="mb-2 block text-sm font-bold text-slate-700">Managed team</label>
                            <select name="team_id" id="team_id" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900">
                                @foreach($userTeams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->sport_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="notes" class="mb-2 block text-sm font-bold text-slate-700">Manager notes</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="w-full rounded-xl border border-slate-200 px-3 py-3 text-sm text-slate-900 placeholder:text-slate-400"
                                      placeholder="Confirm roster availability and application details"></textarea>
                        </div>
                        <button type="submit" class="h-12 w-full rounded-xl bg-slate-950 text-sm font-bold text-white hover:bg-indigo-600">
                            Submit application
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
