<x-app-layout>
    <div class="space-y-8" x-data="adminDashboard()">
        <!-- Brand Hero Header -->
        <section class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-slate-950 text-white shadow-[0_24px_60px_rgba(15,23,42,0.12)]">
            <div class="absolute right-0 top-0 h-full w-2/5 bg-[radial-gradient(circle_at_center,rgba(129,140,248,0.24),transparent_65%)]"></div>
            <div class="relative grid gap-8 px-6 py-8 sm:px-8 lg:grid-cols-[1.45fr_0.55fr] lg:px-10 lg:py-10">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-indigo-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Authority console
                        </p>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-mono font-bold text-slate-300">
                            Broadcasting: Reverb / Log
                        </span>
                    </div>
                    <h1 class="mt-4 text-3xl font-black leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">
                        League Authority <span class="text-indigo-300">Dashboard</span>
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                        Real-time match scoring, tournament entry vetting, and medical clearance inspector from one central command desk.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/10 lg:self-center">
                    <div class="bg-slate-950/75 p-4">
                        <p class="text-3xl font-black tracking-tight text-white">{{ $activeMatches->count() }}</p>
                        <p class="mt-1 text-[11px] font-bold uppercase tracking-wider text-slate-300">Active matches</p>
                    </div>
                    <div class="bg-slate-950/75 p-4">
                        <p class="text-3xl font-black tracking-tight text-white">{{ $pendingApplications->count() }}</p>
                        <p class="mt-1 text-[11px] font-bold uppercase tracking-wider text-slate-300">Pending entries</p>
                    </div>
                    <div class="bg-slate-950/75 p-4">
                        <p class="text-3xl font-black tracking-tight text-white">{{ $injuredPlayers->count() }}</p>
                        <p class="mt-1 text-[11px] font-bold uppercase tracking-wider text-slate-300">Medical holds</p>
                    </div>
                    <div class="bg-slate-950/75 p-4">
                        <p class="text-3xl font-black tracking-tight text-white">{{ $tournaments->count() }}</p>
                        <p class="mt-1 text-[11px] font-bold uppercase tracking-wider text-slate-300">Competitions</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3 Primary Console Columns/Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Col 1: LIVE MATCH CONSOLE (Spans 2 cols) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Live Console Card -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_8px_24px_rgba(15,23,42,0.05)] space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                                </span>
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Live control desk</p>
                            </div>
                            <h2 class="mt-1 text-xl font-black tracking-tight text-slate-950">Official Live Match Console</h2>
                            <p class="mt-1 text-xs text-slate-500">Adjust scores, log key occurrences, and emit real-time WebSocket score broadcasts</p>
                        </div>
                    </div>

                    <!-- Match Selector for scoring -->
                    @if($activeMatches->isNotEmpty())
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Select Match to Control</label>
                                <span class="text-xs font-semibold text-slate-400">{{ $activeMatches->count() }} available</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                @foreach($activeMatches as $m)
                                    <button type="button" @click="selectMatch({{ $m->id }}, '{{ addslashes($m->homeTeam->name) }}', '{{ addslashes($m->awayTeam->name) }}', {{ $m->home_score }}, {{ $m->away_score }}, '{{ $m->status }}')"
                                        class="flex flex-col justify-between rounded-xl border p-3.5 text-left transition"
                                        :class="currentMatchId === {{ $m->id }} ? 'border-indigo-600 bg-indigo-50/70 shadow-sm ring-1 ring-indigo-500 text-slate-950' : 'border-slate-200 bg-slate-50/70 hover:border-slate-300 hover:bg-white text-slate-700'">
                                        <div class="flex items-center justify-between text-xs mb-2">
                                            <span class="font-bold truncate max-w-[140px] text-slate-900">{{ $m->tournament->title ?? 'Exhibition' }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] uppercase font-bold {{ $m->status === 'live' ? 'border border-rose-200 bg-rose-50 text-rose-700' : 'border border-slate-200 bg-slate-100 text-slate-600' }}">
                                                {{ $m->status }}
                                            </span>
                                        </div>
                                        <div class="font-bold text-xs text-slate-950">
                                            {{ $m->homeTeam->name }} <span class="font-extrabold text-indigo-600 tabular-nums">({{ $m->home_score }})</span> <span class="mx-1 text-slate-400 font-medium">vs</span> {{ $m->awayTeam->name }} <span class="font-extrabold text-indigo-600 tabular-nums">({{ $m->away_score }})</span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>

                            <!-- Live Scoring Control Box -->
                            <form :action="'/admin/matches/' + currentMatchId + '/score'" method="POST" class="space-y-5 rounded-2xl border border-slate-200 bg-slate-50/70 p-5 sm:p-6">
                                @csrf
                                <div class="border-b border-slate-200 pb-3 text-center">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">Active Match Controller</div>
                                    <div class="mt-1 text-lg font-black text-slate-950">
                                        <span x-text="homeTeam"></span> <span class="mx-1 text-sm font-bold text-indigo-600">vs</span> <span x-text="awayTeam"></span>
                                    </div>
                                </div>

                                <!-- Interactive Score Steppers -->
                                <div class="grid grid-cols-2 gap-4 items-center">
                                    <!-- Home Stepper -->
                                    <div class="space-y-2 rounded-xl border border-slate-200 bg-white p-4 text-center shadow-sm">
                                        <div class="truncate text-xs font-bold uppercase tracking-wider text-slate-600" x-text="homeTeam"></div>
                                        <div class="font-mono text-4xl font-black tabular-nums text-slate-950" x-text="homeScore"></div>
                                        <input type="hidden" name="home_score" :value="homeScore">
                                        <div class="flex items-center justify-center gap-2 pt-1">
                                            <button type="button" @click="homeScore = Math.max(0, homeScore - 1)" class="h-8 w-8 rounded-lg border border-slate-200 bg-white text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-100 hover:text-slate-950">-1</button>
                                            <button type="button" @click="homeScore = homeScore + 1" class="h-8 w-8 rounded-lg bg-indigo-600 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">+1</button>
                                            <button type="button" @click="homeScore = homeScore + 3" class="h-8 rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 text-xs font-bold text-indigo-700 shadow-sm transition hover:bg-indigo-100">+3</button>
                                        </div>
                                    </div>

                                    <!-- Away Stepper -->
                                    <div class="space-y-2 rounded-xl border border-slate-200 bg-white p-4 text-center shadow-sm">
                                        <div class="truncate text-xs font-bold uppercase tracking-wider text-slate-600" x-text="awayTeam"></div>
                                        <div class="font-mono text-4xl font-black tabular-nums text-slate-950" x-text="awayScore"></div>
                                        <input type="hidden" name="away_score" :value="awayScore">
                                        <div class="flex items-center justify-center gap-2 pt-1">
                                            <button type="button" @click="awayScore = Math.max(0, awayScore - 1)" class="h-8 w-8 rounded-lg border border-slate-200 bg-white text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-100 hover:text-slate-950">-1</button>
                                            <button type="button" @click="awayScore = awayScore + 1" class="h-8 w-8 rounded-lg bg-indigo-600 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">+1</button>
                                            <button type="button" @click="awayScore = awayScore + 3" class="h-8 rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 text-xs font-bold text-indigo-700 shadow-sm transition hover:bg-indigo-100">+3</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Match Status Selector -->
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold uppercase tracking-[0.12em] text-slate-600">Fixture Status State</label>
                                    <select name="status" x-model="status" class="block w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-semibold text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        <option value="upcoming">Upcoming (Pre-match)</option>
                                        <option value="live">Live (Broadcasting actively)</option>
                                        <option value="completed">Completed (Final Score Sealed &amp; Stats Processed)</option>
                                    </select>
                                </div>

                                <!-- Incident / Event Logger -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-slate-600">Match Event Type</label>
                                        <select name="event_type" class="block w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs font-medium text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            <option value="score">Goal / Points Scored</option>
                                            <option value="yellow_card">Yellow Warning Card</option>
                                            <option value="red_card">Red Card Disqualification</option>
                                            <option value="substitution">Player Tactical Substitution</option>
                                            <option value="halftime">Period / Halftime Whistle</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-slate-600">Event Description</label>
                                        <input type="text" name="event_description" placeholder="e.g. 68' Brilliant curved shot into top corner" class="block w-full rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    <span>Emit Official Live Score Update</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-8 text-center text-xs font-medium text-slate-500 bg-slate-50/70 rounded-2xl border border-dashed border-slate-300">
                            No active or upcoming matches available. Schedule new friendlies or launch a tournament bracket.
                        </div>
                    @endif
                </div>

                <!-- Application Review Console -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_8px_24px_rgba(15,23,42,0.05)] space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Registrations</p>
                            <h2 class="mt-1 text-lg font-bold text-slate-950">Tournament Applications Review</h2>
                            <p class="text-xs text-slate-500">Vet team registrations before seeding brackets</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            {{ $pendingApplications->count() }} Pending
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($pendingApplications as $app)
                            <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div>
                                    <div class="font-bold text-slate-950 text-sm">{{ $app->team->name }}</div>
                                    <div class="mt-0.5 text-slate-500">
                                        Tournament: <span class="text-indigo-600 font-semibold">{{ $app->tournament->title }}</span>
                                        &bull; Manager: <span class="text-slate-700">{{ $app->team->manager->name ?? 'Staff' }}</span>
                                    </div>
                                    @if($app->notes)
                                        <p class="text-[11px] text-slate-600 italic mt-1 rounded-lg bg-slate-50 border border-slate-100 px-2.5 py-1.5">"{{ $app->notes }}"</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.applications.review', $app->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                                            ✓ Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.applications.review', $app->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 shadow-sm transition">
                                            ✕ Decline
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs font-medium text-slate-500 bg-slate-50/70 rounded-xl border border-dashed border-slate-300">
                                No pending team applications awaiting review.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Col 2: PLAYER HEALTH INSPECTOR & TOURNAMENTS (1 Col) -->
            <div class="space-y-8">

                <!-- Player Health Inspector Card -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_8px_24px_rgba(15,23,42,0.05)] space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-50 text-xs">🏥</span>
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-rose-600">Medical desk</p>
                            </div>
                            <h2 class="mt-1 text-base font-bold text-slate-950">Player Health Inspector</h2>
                            <p class="text-[11px] text-slate-500">Athletes flagged with medical holds</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            {{ $injuredPlayers->count() }} Injured
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($injuredPlayers as $prof)
                            <div class="p-3.5 bg-rose-50/40 rounded-xl border border-rose-100 text-xs space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-950 text-sm">{{ $prof->user->name }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 text-[10px] font-bold uppercase tracking-wider">Medical Hold</span>
                                </div>

                                <div class="text-[11px] text-slate-500">
                                    Team: <span class="font-medium text-slate-700">{{ $prof->user->teams->pluck('name')->join(', ') ?: 'Free Agent' }}</span>
                                    &bull; Sport: <span class="font-medium text-slate-700">{{ $prof->primary_sport }}</span>
                                </div>

                                @if($prof->injury_notes)
                                    <div class="p-2 bg-white/80 rounded-lg border border-rose-200/60 text-[11px] text-rose-900 italic">
                                        "{{ $prof->injury_notes }}"
                                    </div>
                                @endif

                                <form action="{{ route('admin.players.clearInjury', $prof->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                                        <span>✓</span> Clear Medical Hold (Make Fit)
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs font-medium text-slate-500 bg-slate-50/70 rounded-xl border border-dashed border-slate-300">
                                All registered players are active and medically fit!
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tournaments Quick Overview & Bracket Seed -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_8px_24px_rgba(15,23,42,0.05)] space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Overview</p>
                            <h2 class="mt-1 text-base font-bold text-slate-950">Tournaments Overview</h2>
                        </div>
                        <a href="{{ route('tournaments.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            View All &rarr;
                        </a>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        @forelse($tournaments as $t)
                            <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/70 hover:border-slate-300 hover:bg-white transition">
                                <div class="min-w-0">
                                    <a href="{{ route('tournaments.show', $t->slug) }}" class="font-bold text-slate-950 hover:text-indigo-600 transition block truncate">
                                        {{ $t->title }}
                                    </a>
                                    <span class="text-[11px] text-slate-500">{{ $t->teams_count }} Teams &bull; <span class="capitalize">{{ str_replace('_', ' ', $t->current_stage) }}</span></span>
                                </div>

                                <form action="{{ route('tournaments.generateBracket', $t->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="shrink-0 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-bold text-[11px] shadow-sm transition" title="Seed and Generate Fixtures">
                                        Seed Bracket
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs font-medium text-slate-500 bg-slate-50/70 rounded-xl border border-dashed border-slate-300">
                                No tournaments created yet.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function adminDashboard() {
            return {
                @if($activeMatches->isNotEmpty())
                    currentMatchId: {{ $activeMatches->first()->id }},
                    homeTeam: '{{ addslashes($activeMatches->first()->homeTeam->name) }}',
                    awayTeam: '{{ addslashes($activeMatches->first()->awayTeam->name) }}',
                    homeScore: {{ $activeMatches->first()->home_score }},
                    awayScore: {{ $activeMatches->first()->away_score }},
                    status: '{{ $activeMatches->first()->status }}',
                @else
                    currentMatchId: null,
                    homeTeam: '',
                    awayTeam: '',
                    homeScore: 0,
                    awayScore: 0,
                    status: 'upcoming',
                @endif
                selectMatch(id, home, away, hScore, aScore, st) {
                    this.currentMatchId = id;
                    this.homeTeam = home;
                    this.awayTeam = away;
                    this.homeScore = hScore;
                    this.awayScore = aScore;
                    this.status = st;
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

