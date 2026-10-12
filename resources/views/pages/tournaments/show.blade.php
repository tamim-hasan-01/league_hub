<x-app-layout>
    <div class="space-y-10" x-data="{ activeTab: 'bracket' }">
        <!-- Tournament Header Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gray-800 border border-gray-700 p-6 sm:p-8 shadow-2xl">
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div class="space-y-3 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/15 text-cyan-400 border border-cyan-500/30">
                            {{ $tournament->sport_type }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-700 text-gray-300 border border-gray-600 uppercase tracking-wider">
                            Status: {{ str_replace('_', ' ', $tournament->status) }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-500/10 text-sky-400 border border-sky-500/30 uppercase tracking-wider">
                            Phase: {{ str_replace('_', ' ', $tournament->current_stage) }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">{{ $tournament->title }}</h1>
                    <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">{{ $tournament->description }}</p>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 pt-2">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            {{ $tournament->venue ?? 'Main Championship Arena' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Prize: {{ $tournament->prize_pool ?? '$25,000' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $tournament->teams->count() }} / {{ $tournament->max_teams }} Teams Enrolled
                        </span>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                    @auth
                        <!-- Follow / Bookmark Tournament Button for Viewers -->
                        <form action="{{ route('bookmarks.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="tournament">
                            <input type="hidden" name="id" value="{{ $tournament->id }}">
                            @php
                            $isFollowed = auth()->user()->bookmarks()->where('tournament_id', $tournament->id)->exists();
                            @endphp
                            <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-semibold border transition flex items-center justify-center gap-1.5 {{ $isFollowed ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-gray-700 hover:bg-gray-600 text-gray-200 border-gray-600' }}">
                                <svg class="w-4 h-4 {{ $isFollowed ? 'fill-cyan-400 text-cyan-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                                <span>{{ $isFollowed ? 'Following Updates' : 'Follow Tournament' }}</span>
                            </button>
                        </form>

                        <!-- Manager Register Team Button -->
                        @if(auth()->user()->isManager() && $tournament->status === 'registration_open')
                            <button data-modal-target="apply-team-modal" data-modal-toggle="apply-team-modal" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-gray-950 shadow-md shadow-cyan-500/20 transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                <span>Apply Team</span>
                            </button>
                        @endif

                        <!-- Admin Generate Bracket Fixtures -->
                        @can('create-tournament')
                            <form action="{{ route('tournaments.generateBracket', $tournament->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-sky-600 hover:bg-sky-500 text-white shadow-md transition flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    <span>Seed & Generate Brackets</span>
                                </button>
                            </form>
                        @endcan
                    @endauth
                </div>
            </div>
        </div>

        <!-- Section Navigation Tabs -->
        <div class="flex items-center gap-3 border-b border-gray-800 pb-2">
            <button @click="activeTab = 'bracket'" :class="activeTab === 'bracket' ? 'text-cyan-400 border-b-2 border-cyan-400 font-bold' : 'text-gray-400 hover:text-white'" class="px-4 py-2 text-sm transition">
                Interactive Bracket Tree
            </button>
            <button @click="activeTab = 'teams'" :class="activeTab === 'teams' ? 'text-cyan-400 border-b-2 border-cyan-400 font-bold' : 'text-gray-400 hover:text-white'" class="px-4 py-2 text-sm transition">
                Enrolled Teams ({{ $tournament->teams->count() }})
            </button>
            <button @click="activeTab = 'fixtures'" :class="activeTab === 'fixtures' ? 'text-cyan-400 border-b-2 border-cyan-400 font-bold' : 'text-gray-400 hover:text-white'" class="px-4 py-2 text-sm transition">
                All Fixtures ({{ $tournament->matches->count() }})
            </button>
        </div>

        <!-- TAB 1: Visual Interactive Knockout Bracket Tree -->
        <div x-show="activeTab === 'bracket'" class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">Knockout Stage Progression</h2>
                    <p class="text-xs text-gray-400">Official tournament tree progressing from Quarterfinals to the Grand Final</p>
                </div>
            </div>

            <!-- Visual Knockout Tree Grid -->
            <div class="overflow-x-auto pb-6">
                <div class="min-w-[760px] grid grid-cols-3 gap-6 items-center">

                    <!-- Col 1: Quarter Finals -->
                    <div class="space-y-4">
                        <div class="text-xs font-bold text-cyan-400 uppercase tracking-wider text-center py-2 bg-gray-800/80 rounded-lg border border-gray-700">
                            Quarter Finals
                        </div>
                        @forelse($bracketTree['quarter_final'] ?? [] as $m)
                            <div class="bg-gray-800 border border-gray-700/80 rounded-xl p-3 shadow-md space-y-2 relative">
                                <div class="flex items-center justify-between text-[11px] text-gray-400 border-b border-gray-700/60 pb-1">
                                    <span>QF Match #{{ $loop->iteration }}</span>
                                    <span class="text-cyan-400 font-mono">{{ $m['scheduled_at'] }}</span>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex items-center justify-between p-1.5 rounded {{ $m['winner_id'] === $m['home_team']['id'] ? 'bg-cyan-500/15 font-bold text-white' : 'text-gray-300' }}">
                                        <span class="truncate">{{ $m['home_team']['name'] ?? 'TBD' }}</span>
                                        <span class="font-mono font-bold text-cyan-400">{{ $m['home_team']['score'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between p-1.5 rounded {{ $m['winner_id'] === $m['away_team']['id'] ? 'bg-cyan-500/15 font-bold text-white' : 'text-gray-300' }}">
                                        <span class="truncate">{{ $m['away_team']['name'] ?? 'TBD' }}</span>
                                        <span class="font-mono font-bold text-cyan-400">{{ $m['away_team']['score'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-gray-500 bg-gray-800/40 rounded-xl border border-gray-800">
                                Quarterfinal fixtures will seed once 8 teams are approved.
                            </div>
                        @endforelse
                    </div>

                    <!-- Col 2: Semi Finals -->
                    <div class="space-y-6">
                        <div class="text-xs font-bold text-sky-400 uppercase tracking-wider text-center py-2 bg-gray-800/80 rounded-lg border border-gray-700">
                            Semi Finals
                        </div>
                        @forelse($bracketTree['semi_final'] ?? [] as $m)
                            <div class="bg-gray-800 border border-gray-700/80 rounded-xl p-3 shadow-md space-y-2">
                                <div class="flex items-center justify-between text-[11px] text-gray-400 border-b border-gray-700/60 pb-1">
                                    <span>SF Match #{{ $loop->iteration }}</span>
                                    <span class="text-sky-400 font-mono">{{ $m['scheduled_at'] }}</span>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex items-center justify-between p-1.5 rounded {{ $m['winner_id'] === $m['home_team']['id'] ? 'bg-sky-500/15 font-bold text-white' : 'text-gray-300' }}">
                                        <span class="truncate">{{ $m['home_team']['name'] ?? 'TBD Winner' }}</span>
                                        <span class="font-mono font-bold text-sky-400">{{ $m['home_team']['score'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between p-1.5 rounded {{ $m['winner_id'] === $m['away_team']['id'] ? 'bg-sky-500/15 font-bold text-white' : 'text-gray-300' }}">
                                        <span class="truncate">{{ $m['away_team']['name'] ?? 'TBD Winner' }}</span>
                                        <span class="font-mono font-bold text-sky-400">{{ $m['away_team']['score'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-gray-500 bg-gray-800/40 rounded-xl border border-gray-800">
                                Pending conclusion of Quarterfinal matches.
                            </div>
                        @endforelse
                    </div>

                    <!-- Col 3: Grand Final -->
                    <div class="space-y-4">
                        <div class="text-xs font-bold text-amber-400 uppercase tracking-wider text-center py-2 bg-gradient-to-r from-amber-500/20 to-yellow-500/20 rounded-lg border border-amber-500/30">
                            🏆 Grand Championship Final
                        </div>
                        @forelse($bracketTree['final'] ?? [] as $m)
                            <div class="bg-gradient-to-b from-gray-800 to-gray-850 border border-amber-500/40 rounded-2xl p-4 shadow-xl space-y-3 ring-1 ring-amber-500/20">
                                <div class="flex items-center justify-between text-xs text-amber-300 pb-1.5 border-b border-gray-700">
                                    <span class="font-bold">Championship Title Match</span>
                                    <span class="font-mono">{{ $m['scheduled_at'] }}</span>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center justify-between p-2 rounded-lg {{ $m['winner_id'] === $m['home_team']['id'] ? 'bg-amber-500/20 font-bold text-white' : 'text-gray-200' }}">
                                        <span class="truncate">{{ $m['home_team']['name'] ?? 'Finalist 1' }}</span>
                                        <span class="font-mono font-bold text-amber-400 text-base">{{ $m['home_team']['score'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between p-2 rounded-lg {{ $m['winner_id'] === $m['away_team']['id'] ? 'bg-amber-500/20 font-bold text-white' : 'text-gray-200' }}">
                                        <span class="truncate">{{ $m['away_team']['name'] ?? 'Finalist 2' }}</span>
                                        <span class="font-mono font-bold text-amber-400 text-base">{{ $m['away_team']['score'] ?? 0 }}</span>
                                    </div>
                                </div>
                                @if($m['winner_id'])
                                    <div class="text-center pt-2 text-xs font-black text-amber-300">
                                        👑 TOURNAMENT CHAMPION DECLARED
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-gray-500 bg-gray-800/40 rounded-xl border border-gray-800">
                                Final will unlock when Semifinal winners are confirmed.
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

        <!-- TAB 2: Enrolled Teams Roster -->
        <div x-show="activeTab === 'teams'" x-cloak class="space-y-4">
            <h2 class="text-xl font-bold text-white">Approved Team Rosters</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($tournament->teams as $team)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <a href="{{ route('teams.show', $team->slug) }}" class="font-bold text-white hover:text-cyan-400 transition block">
                                {{ $team->name }}
                            </a>
                            <span class="text-xs text-gray-400">Manager: {{ $team->manager->name ?? 'Staff' }}</span>
                            <div class="text-[11px] text-cyan-400 mt-1">Ranking: #{{ $team->areal_ranking }}</div>
                        </div>
                        <a href="{{ route('teams.show', $team->slug) }}" class="p-2 rounded-lg bg-gray-700 hover:bg-cyan-500/20 text-gray-300 hover:text-cyan-400 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 bg-gray-800/40 rounded-xl border border-gray-700 text-gray-400 text-xs">
                        No teams currently confirmed for this tournament yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 3: Fixtures & Match Results -->
        <div x-show="activeTab === 'fixtures'" x-cloak class="space-y-4">
            <h2 class="text-xl font-bold text-white">All Fixtures and Results</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($tournament->matches as $match)
                    <x-match-card :match="$match" />
                @empty
                    <div class="col-span-3 text-center py-8 bg-gray-800/40 rounded-xl border border-gray-700 text-gray-400 text-xs">
                        No matches scheduled yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Manager Apply Team Modal -->
    @if(auth()->check() && auth()->user()->isManager())
    <div id="apply-team-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-700">
                    <h3 class="text-base font-bold text-white">Enroll Team in {{ $tournament->title }}</h3>
                    <button type="button" class="text-gray-400 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" data-modal-hide="apply-team-modal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>

                <form action="{{ route('tournaments.apply', $tournament->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="team_id" class="block mb-1 text-xs font-medium text-gray-300">Select Managed Team</label>
                        <select name="team_id" id="team_id" required class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                            @foreach($userTeams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->sport_type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block mb-1 text-xs font-medium text-gray-300">Manager Pitch & Roster Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5" placeholder="Confirming availability of full starting roster..."></textarea>
                    </div>

                    <button type="submit" class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition">
                        Submit Official Application
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>

