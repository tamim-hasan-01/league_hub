<x-app-layout>
    <div class="space-y-8" x-data="adminDashboard()">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-3xl bg-gray-800 border border-cyan-500/30 p-6 sm:p-8 shadow-2xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">Restricted Operations Console</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white">League Authority Dashboard</h1>
                    <p class="text-xs sm:text-sm text-gray-400 mt-1">Real-time match scoring, tournament entry vetting, and medical clearance inspector</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-gray-900 text-cyan-400 border border-gray-700">
                        Broadcasting: Reverb / Log
                    </span>
                </div>
            </div>
        </div>

        <!-- 3 Primary Console Columns/Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Col 1: LIVE MATCH CONSOLE (Spans 2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Live Console Card -->
                <div class="bg-gray-800 border border-gray-700 rounded-3xl p-6 shadow-xl space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-700">
                        <div>
                            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-cyan-500"></span>
                                </span>
                                Official Live Match Console
                            </h2>
                            <p class="text-xs text-gray-400">Adjust scores, log key occurrences, and emit real-time WebSocket score broadcasts</p>
                        </div>
                    </div>

                    <!-- Match Selector for scoring -->
                    @if($activeMatches->isNotEmpty())
                        <div class="space-y-4">
                            <label class="block text-xs font-semibold text-gray-300 uppercase">Select Match To Control</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($activeMatches as $m)
                                    <button type="button" @click="selectMatch({{ $m->id }}, '{{ addslashes($m->homeTeam->name) }}', '{{ addslashes($m->awayTeam->name) }}', {{ $m->home_score }}, {{ $m->away_score }}, '{{ $m->status }}')"
                                        class="p-3 rounded-xl border text-left transition flex flex-col justify-between"
                                        :class="currentMatchId === {{ $m->id }} ? 'bg-cyan-500/15 border-cyan-500 ring-1 ring-cyan-500 text-white' : 'bg-gray-900/60 border-gray-700 hover:border-gray-600 text-gray-300'">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="font-bold truncate max-w-[140px]">{{ $m->tournament->title ?? 'Exhibition' }}</span>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold {{ $m->status === 'live' ? 'bg-rose-500/20 text-rose-400' : 'bg-gray-800 text-gray-400' }}">
                                                {{ $m->status }}
                                            </span>
                                        </div>
                                        <div class="font-bold text-xs">
                                            {{ $m->homeTeam->name }} <span class="text-cyan-400">({{ $m->home_score }})</span> vs {{ $m->awayTeam->name }} <span class="text-cyan-400">({{ $m->away_score }})</span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>

                            <!-- Live Scoring Control Box -->
                            <form :action="'/admin/matches/' + currentMatchId + '/score'" method="POST" class="p-5 rounded-2xl bg-gray-900/90 border border-cyan-500/30 space-y-5">
                                @csrf
                                <div class="text-center pb-2 border-b border-gray-800">
                                    <div class="text-xs text-gray-400 uppercase font-semibold">Active Match Controller</div>
                                    <div class="text-lg font-black text-white mt-0.5">
                                        <span x-text="homeTeam"></span> <span class="text-cyan-400 font-bold">vs</span> <span x-text="awayTeam"></span>
                                    </div>
                                </div>

                                <!-- Interactive Score Steppers -->
                                <div class="grid grid-cols-2 gap-6 items-center">
                                    <!-- Home Stepper -->
                                    <div class="bg-gray-800/80 p-4 rounded-xl border border-gray-700 text-center space-y-2">
                                        <div class="text-xs font-bold text-gray-300 truncate" x-text="homeTeam"></div>
                                        <div class="text-4xl font-black text-cyan-400 font-mono" x-text="homeScore"></div>
                                        <input type="hidden" name="home_score" :value="homeScore">
                                        <div class="flex items-center justify-center gap-2 pt-1">
                                            <button type="button" @click="homeScore = Math.max(0, homeScore - 1)" class="w-8 h-8 rounded-lg bg-gray-700 hover:bg-gray-600 font-bold text-sm text-white">-1</button>
                                            <button type="button" @click="homeScore = homeScore + 1" class="w-8 h-8 rounded-lg bg-cyan-600 hover:bg-cyan-500 font-bold text-sm text-white">+1</button>
                                            <button type="button" @click="homeScore = homeScore + 3" class="px-2 h-8 rounded-lg bg-cyan-900 hover:bg-cyan-800 font-bold text-xs text-cyan-300">+3</button>
                                        </div>
                                    </div>

                                    <!-- Away Stepper -->
                                    <div class="bg-gray-800/80 p-4 rounded-xl border border-gray-700 text-center space-y-2">
                                        <div class="text-xs font-bold text-gray-300 truncate" x-text="awayTeam"></div>
                                        <div class="text-4xl font-black text-sky-400 font-mono" x-text="awayScore"></div>
                                        <input type="hidden" name="away_score" :value="awayScore">
                                        <div class="flex items-center justify-center gap-2 pt-1">
                                            <button type="button" @click="awayScore = Math.max(0, awayScore - 1)" class="w-8 h-8 rounded-lg bg-gray-700 hover:bg-gray-600 font-bold text-sm text-white">-1</button>
                                            <button type="button" @click="awayScore = awayScore + 1" class="w-8 h-8 rounded-lg bg-sky-600 hover:bg-sky-500 font-bold text-sm text-white">+1</button>
                                            <button type="button" @click="awayScore = awayScore + 3" class="px-2 h-8 rounded-lg bg-sky-900 hover:bg-sky-800 font-bold text-xs text-sky-300">+3</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Match Status Selector -->
                                <div>
                                    <label class="block mb-1.5 text-xs font-medium text-gray-300">Fixture Status State</label>
                                    <select name="status" x-model="status" class="bg-gray-800 border border-gray-700 text-white text-xs rounded-lg block w-full p-2.5">
                                        <option value="upcoming">Upcoming (Pre-match)</option>
                                        <option value="live">Live (Broadcasting actively)</option>
                                        <option value="completed">Completed (Final Score Sealed & Stats Processed)</option>
                                    </select>
                                </div>

                                <!-- Incident / Event Logger -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block mb-1 text-xs text-gray-400">Match Event Type</label>
                                        <select name="event_type" class="bg-gray-800 border border-gray-700 text-white text-xs rounded-lg block w-full p-2">
                                            <option value="score">Goal / Points Scored</option>
                                            <option value="yellow_card">Yellow Warning Card</option>
                                            <option value="red_card">Red Card Disqualification</option>
                                            <option value="substitution">Player Tactical Substitution</option>
                                            <option value="halftime">Period / Halftime Whistle</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-xs text-gray-400">Event Description</label>
                                        <input type="text" name="event_description" placeholder="e.g. 68' Brilliant curved shot into top corner" class="bg-gray-800 border border-gray-700 text-white text-xs rounded-lg block w-full p-2">
                                    </div>
                                </div>

                                <button type="submit" class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 font-bold rounded-xl text-sm px-5 py-3 text-center shadow-lg shadow-cyan-500/20 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    <span>Emit Official Live Score Update</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-8 text-center text-xs text-gray-400 bg-gray-900/40 rounded-2xl border border-gray-700">
                            No active or upcoming matches available. Schedule new friendlies or launch a tournament bracket.
                        </div>
                    @endif
                </div>

                <!-- Application Review Console -->
                <div class="bg-gray-800 border border-gray-700 rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-700">
                        <div>
                            <h2 class="text-lg font-bold text-white">Tournament Applications Review</h2>
                            <p class="text-xs text-gray-400">Vet team registrations before seeding brackets</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                            {{ $pendingApplications->count() }} Pending
                        </span>
                    </div>

                    <div class="divide-y divide-gray-700">
                        @forelse($pendingApplications as $app)
                            <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div>
                                    <div class="font-bold text-white text-sm">{{ $app->team->name }}</div>
                                    <div class="text-gray-400">
                                        Tournament: <span class="text-cyan-400 font-semibold">{{ $app->tournament->title }}</span>
                                        &bull; Manager: {{ $app->team->manager->name ?? 'Staff' }}
                                    </div>
                                    @if($app->notes)
                                        <p class="text-[11px] text-gray-300 italic mt-0.5">"{{ $app->notes }}"</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.applications.review', $app->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition">
                                            ✓ Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.applications.review', $app->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-600/80 hover:bg-rose-600 text-white transition">
                                            ✕ Decline
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-gray-400">
                                No pending team applications awaiting review.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Col 2: PLAYER HEALTH INSPECTOR & TOURNAMENTS (1 Col) -->
            <div class="space-y-6">

                <!-- Player Health Inspector Card -->
                <div class="bg-gray-800 border border-gray-700 rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-700">
                        <div>
                            <h2 class="text-base font-bold text-white flex items-center gap-1.5">
                                <span class="text-red-400">🏥</span>
                                Player Health Inspector
                            </h2>
                            <p class="text-[11px] text-gray-400">Athletes flagged with medical holds</p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-red-500/20 text-red-400 border border-red-500/30">
                            {{ $injuredPlayers->count() }} Injured
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($injuredPlayers as $prof)
                            <div class="p-3 bg-gray-900/80 rounded-xl border border-red-900/40 text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-white">{{ $prof->user->name }}</span>
                                    <span class="text-red-400 text-[10px] font-bold uppercase">Medical Hold</span>
                                </div>

                                <div class="text-[11px] text-gray-400">
                                    Team: {{ $prof->user->teams->pluck('name')->join(', ') ?: 'Free Agent' }}
                                    &bull; Sport: {{ $prof->primary_sport }}
                                </div>

                                @if($prof->injury_notes)
                                    <div class="p-2 bg-red-950/40 rounded border border-red-900/30 text-[11px] text-red-200 italic">
                                        "{{ $prof->injury_notes }}"
                                    </div>
                                @endif

                                <form action="{{ route('admin.players.clearInjury', $prof->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-1.5 px-2 rounded-lg bg-emerald-600/80 hover:bg-emerald-600 text-white font-semibold text-[11px] transition">
                                        ✓ Clear Medical Hold (Make Fit)
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-gray-400">
                                All registered players are active and medically fit!
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tournaments Quick Overview & Bracket Seed -->
                <div class="bg-gray-800 border border-gray-700 rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-700">
                        <h2 class="text-base font-bold text-white">Tournaments Overview</h2>
                        <a href="{{ route('tournaments.index') }}" class="text-xs text-cyan-400 hover:underline">View All</a>
                    </div>

                    <div class="space-y-3 text-xs">
                        @foreach($tournaments as $t)
                            <div class="p-3 bg-gray-900/60 rounded-xl border border-gray-700/60 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('tournaments.show', $t->slug) }}" class="font-bold text-white hover:text-cyan-400 transition block">
                                        {{ $t->title }}
                                    </a>
                                    <span class="text-gray-400">{{ $t->teams_count }} Teams &bull; {{ $t->current_stage }}</span>
                                </div>

                                <form action="{{ route('tournaments.generateBracket', $t->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded bg-sky-900 hover:bg-sky-800 text-sky-200 font-semibold text-[10px] transition" title="Seed and Generate Fixtures">
                                        Seed Bracket
                                    </button>
                                </form>
                            </div>
                        @endforeach
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

