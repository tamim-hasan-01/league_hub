<x-app-layout>
    <div class="space-y-8" x-data="{ tab: 'roster' }">
        @php $record = $team->record; @endphp

        {{-- ========================================== --}}
        {{-- Section: Team Profile Hero & Standing Badge --}}
        {{-- Displays club identity, ranking tier, manager, and action controls --}}
        {{-- ========================================== --}}
        <section class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-slate-950 text-white">
            <div class="absolute right-0 top-0 h-full w-2/5 bg-[radial-gradient(circle_at_center,rgba(129,140,248,0.24),transparent_68%)]"></div>
            <div class="relative grid gap-8 px-6 py-8 sm:px-8 lg:grid-cols-[minmax(0,1fr)_280px] lg:px-10 lg:py-10">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white text-xl font-black text-slate-950">
                            {{ strtoupper(substr($team->name, 0, 2)) }}
                        </span>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full border border-indigo-300/20 bg-indigo-300/10 px-3 py-1 text-xs font-bold text-indigo-200">{{ $team->sport_type }}</span>
                                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-bold text-slate-200">Rank #{{ $team->areal_ranking }}</span>
                            </div>
                            <h1 class="mt-2 text-3xl font-black leading-tight tracking-tight sm:text-4xl lg:text-5xl">{{ $team->name }}</h1>
                        </div>
                    </div>
                    {{-- Team Attributes Overview --}}
                    <div class="mt-6 grid gap-px overflow-hidden rounded-xl border border-white/10 bg-white/10 sm:grid-cols-3">
                        <div class="bg-slate-950/75 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Home venue</p>
                            <p class="mt-2 text-sm font-bold text-white">{{ $team->home_venue ?: 'Venue TBA' }}</p>
                        </div>
                        <div class="bg-slate-950/75 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Manager</p>
                            <p class="mt-2 text-sm font-bold text-white">{{ $team->manager->name ?? 'League staff' }}</p>
                        </div>
                        <div class="bg-slate-950/75 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Roster size</p>
                            <p class="mt-2 text-sm font-bold text-white">{{ $team->players->count() }} players</p>
                        </div>
                    </div>
                </div>

                {{-- Manager Action Toolbar --}}
                <aside class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 lg:self-start">
                    @can('manage-team', $team)
                        <button type="button" data-modal-target="recruit-player-modal" data-modal-toggle="recruit-player-modal"
                                class="flex h-12 w-full items-center justify-center rounded-xl bg-white text-sm font-bold text-slate-950 hover:bg-indigo-100">
                            Recruit player
                        </button>
                        <button type="button" data-modal-target="propose-friendly-modal" data-modal-toggle="propose-friendly-modal"
                                class="flex h-12 w-full items-center justify-center rounded-xl border border-white/15 bg-white/5 text-sm font-bold text-white hover:bg-white/10">
                            Propose friendly
                        </button>
                        <a href="{{ route('teams.edit', $team->id) }}" class="flex h-12 w-full items-center justify-center rounded-xl border border-indigo-300/30 bg-indigo-300/10 text-sm font-bold text-indigo-100 hover:bg-indigo-300/20">
                            Edit team
                        </a>
                    @endcan
                </aside>
            </div>
        </section>

        {{-- ========================================== --}}
        {{-- Section: Cumulative Record Counter         --}}
        {{-- Tabulates completed fixtures, total wins, losses, and drawn games --}}
        {{-- ========================================== --}}
        <section class="grid grid-cols-2 overflow-hidden rounded-2xl border border-slate-200 bg-slate-200 sm:grid-cols-4">
            @foreach([
                ['Fixtures', $record['total'], 'text-slate-950'],
                ['Wins', $record['wins'], 'text-emerald-700'],
                ['Losses', $record['losses'], 'text-rose-700'],
                ['Draws', $record['draws'], 'text-slate-700'],
            ] as [$label, $value, $valueClass])
                <div class="bg-white px-5 py-5 text-center">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">{{ $label }}</p>
                    <p class="mt-2 text-3xl font-black tabular-nums {{ $valueClass }}">{{ $value }}</p>
                </div>
            @endforeach
        </section>

        {{-- ========================================== --}}
        {{-- Section: Team Profile Sub-Navigation Tabs  --}}
        {{-- ========================================== --}}
        <nav class="overflow-x-auto border-b border-slate-200" aria-label="Team sections">
            <div class="flex min-w-max gap-1 sm:gap-2">
                <button type="button" @click="tab = 'roster'"
                        :class="tab === 'roster' ? 'border-slate-950 text-slate-950' : 'border-transparent text-slate-500 hover:text-slate-900'"
                        class="border-b-2 px-4 py-3 text-sm font-bold transition">
                    Roster <span class="ml-1 text-slate-400">{{ $team->players->count() }}</span>
                </button>
                <button type="button" @click="tab = 'matches'"
                        :class="tab === 'matches' ? 'border-slate-950 text-slate-950' : 'border-transparent text-slate-500 hover:text-slate-900'"
                        class="border-b-2 px-4 py-3 text-sm font-bold transition">
                    Matches <span class="ml-1 text-slate-400">{{ $matches->count() }}</span>
                </button>
                <button type="button" @click="tab = 'tournaments'"
                        :class="tab === 'tournaments' ? 'border-slate-950 text-slate-950' : 'border-transparent text-slate-500 hover:text-slate-900'"
                        class="border-b-2 px-4 py-3 text-sm font-bold transition">
                    Tournaments <span class="ml-1 text-slate-400">{{ $team->tournaments->count() }}</span>
                </button>
            </div>
        </nav>

        {{-- ========================================== --}}
        {{-- Section: Squad Roster Grid & Player Cards  --}}
        {{-- Shows jersey numbers, playing positions, health statuses, and form stats --}}
        {{-- ========================================== --}}
        <div x-show="tab === 'roster'" class="space-y-5">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Squad</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Roster and player status</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($team->players as $player)
                    @php $profile = $player->playerProfile; @endphp
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-sm font-black text-slate-700">
                                    #{{ $profile->jersey_number ?? '-' }}
                                </span>
                                <div class="min-w-0">
                                    <h3 class="truncate font-black text-slate-950">{{ $player->name }}</h3>
                                    <p class="mt-1 truncate text-sm text-slate-500">{{ $player->pivot->position ?: 'Starter' }}</p>
                                </div>
                            </div>
                            @if($profile && $profile->is_injured)
                                <span class="shrink-0 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700">Injured</span>
                            @else
                                <span class="shrink-0 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Active</span>
                            @endif
                        </div>

                        {{-- Player Medical Clearance Notes --}}
                        @if($profile && $profile->is_injured && $profile->injury_notes)
                            <div class="mt-4 rounded-xl border border-rose-100 bg-rose-50 p-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.10em] text-rose-500">Medical notes</p>
                                <p class="mt-1 text-xs leading-5 text-rose-700">{{ $profile->injury_notes }}</p>
                            </div>
                        @endif

                        {{-- Rolling Form & Performance Metrics --}}
                        @if($profile && $profile->career_stats)
                            <div class="mt-4 grid grid-cols-3 overflow-hidden rounded-xl border border-slate-200 bg-slate-50 text-center">
                                <div class="px-2 py-3">
                                    <p class="text-[10px] font-bold uppercase text-slate-400">Form</p>
                                    <p class="mt-1 text-sm font-black text-indigo-700">{{ $profile->career_stats['form_rating'] ?? '-' }}</p>
                                </div>
                                <div class="border-x border-slate-200 px-2 py-3">
                                    <p class="text-[10px] font-bold uppercase text-slate-400">Matches</p>
                                    <p class="mt-1 text-sm font-black text-slate-900">{{ $profile->career_stats['matches_played'] ?? 0 }}</p>
                                </div>
                                <div class="px-2 py-3">
                                    <p class="text-[10px] font-bold uppercase text-slate-400">Goals</p>
                                    <p class="mt-1 text-sm font-black text-slate-900">{{ $profile->career_stats['goals_or_points'] ?? 0 }}</p>
                                </div>
                            </div>
                        @endif

                        @can('manage-team', $team)
                            <form action="{{ route('teams.removePlayer', [$team->id, $player->id]) }}" method="POST" class="mt-4 border-t border-slate-100 pt-4" onsubmit="return confirm('Remove player from roster?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-bold text-rose-600 hover:text-rose-800">Remove from roster</button>
                            </form>
                        @endcan
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                        <p class="text-base font-bold text-slate-700">No players in this roster</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- Section: Team Match Fixtures & History     --}}
        {{-- ========================================== --}}
        <div x-show="tab === 'matches'" x-cloak class="space-y-5">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Team fixtures</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Matches and results</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($matches as $match)
                    <x-match-card :match="$match" />
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                        <p class="text-base font-bold text-slate-700">No fixtures recorded</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- Section: Tournament Competition Affiliations --}}
        {{-- ========================================== --}}
        <div x-show="tab === 'tournaments'" x-cloak class="space-y-5">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Competition history</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Tournament affiliations</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @forelse($team->tournaments as $tournament)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">{{ $tournament->sport_type }}</p>
                        <h3 class="mt-2 text-lg font-black text-slate-950">{{ $tournament->title }}</h3>
                        <p class="mt-1 text-sm capitalize text-slate-500">{{ str_replace('_', ' ', $tournament->current_stage) }} stage</p>
                        <a href="{{ route('tournaments.show', $tournament->slug) }}" class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-sm font-bold text-indigo-600">
                            View tournament <span>&rarr;</span>
                        </a>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                        <p class="text-base font-bold text-slate-700">No tournament affiliations</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- Section: Manager Modals (Recruit Athlete & Propose Friendly) --}}
    {{-- ========================================== --}}
    @can('manage-team', $team)
        <div id="recruit-player-modal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto md:inset-0">
            <div class="relative w-full max-w-lg p-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-indigo-600">Roster</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Recruit athlete</h3>
                        </div>
                        <button type="button" data-modal-hide="recruit-player-modal" aria-label="Close recruitment" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500">&times;</button>
                    </div>
                    <form action="{{ route('teams.addPlayer', $team->id) }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="player_id" class="mb-2 block text-sm font-bold text-slate-700">Available player</label>
                            <select name="player_id" id="player_id" required class="h-12 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900">
                                @foreach($availablePlayers as $player)
                                    <option value="{{ $player->id }}">{{ $player->name }} (Rank #{{ $player->playerProfile->areal_ranking ?? '50' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="position" class="mb-2 block text-sm font-bold text-slate-700">Roster position</label>
                            <input type="text" name="position" id="position" required class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900" placeholder="Starting forward, defender, goalie...">
                        </div>
                        <button type="submit" class="h-12 w-full rounded-xl bg-slate-950 text-sm font-bold text-white hover:bg-indigo-600">Confirm roster contract</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="propose-friendly-modal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto md:inset-0">
            <div class="relative w-full max-w-lg p-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-indigo-600">Friendly match</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Propose an exhibition</h3>
                        </div>
                        <button type="button" data-modal-hide="propose-friendly-modal" aria-label="Close friendly match" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500">&times;</button>
                    </div>
                    <form action="{{ route('teams.proposeFriendly', $team->id) }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="opponent_team_id" class="mb-2 block text-sm font-bold text-slate-700">Opponent team</label>
                            <select name="opponent_team_id" id="opponent_team_id" required class="h-12 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900">
                                @foreach($otherTeams as $opponent)
                                    <option value="{{ $opponent->id }}">{{ $opponent->name }} (Rank #{{ $opponent->areal_ranking }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="scheduled_at" class="mb-2 block text-sm font-bold text-slate-700">Date and time</label>
                                <input type="datetime-local" name="scheduled_at" id="scheduled_at" required class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900">
                            </div>
                            <div>
                                <label for="venue" class="mb-2 block text-sm font-bold text-slate-700">Venue</label>
                                <input type="text" name="venue" id="venue" value="{{ $team->home_venue }}" required class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900">
                            </div>
                        </div>
                        <button type="submit" class="h-12 w-full rounded-xl bg-slate-950 text-sm font-bold text-white hover:bg-indigo-600">Dispatch callups</button>
                    </form>
                </div>
            </div>
        </div>
    @endcan
</x-app-layout>
