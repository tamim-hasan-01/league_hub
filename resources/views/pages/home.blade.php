<x-app-layout>
    <div class="space-y-10">

        <!-- Hero Section with Glowing Accents -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-gray-800/90 to-gray-900 border border-gray-700/80 p-6 sm:p-10 shadow-2xl">
            <!-- Glow effect circles -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold uppercase tracking-wider mb-4">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Next-Gen League & Tournament Management
                </div>

                <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                    Dominate the Bracket. <br>
                    <span class="bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-500 bg-clip-text text-transparent">
                        Track Real-Time Glory.
                    </span>
                </h1>

                <p class="mt-4 text-sm sm:text-base text-gray-300 leading-relaxed max-w-2xl">
                    Unified platform for league organizers, managers, and athletes. Manage rosters, schedule friendly matches, follow multi-sport knockout brackets, and track live scores with sub-second accuracy.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('tournaments.index') }}" class="px-6 py-3 rounded-xl font-bold text-sm bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 hover:to-sky-300 text-gray-950 shadow-lg shadow-cyan-500/25 transition-all flex items-center gap-2">
                        <span>Explore Tournaments</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>

                    <a href="{{ route('teams.index') }}" class="px-6 py-3 rounded-xl font-semibold text-sm bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 transition flex items-center gap-2">
                        <span>Browse Rosters</span>
                    </a>

                    @can('create-tournament')
                        <button data-modal-target="create-tournament-modal" data-modal-toggle="create-tournament-modal" class="px-5 py-3 rounded-xl font-bold text-sm bg-cyan-950/60 hover:bg-cyan-900/60 text-cyan-400 border border-cyan-500/40 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Create New League</span>
                        </button>
                    @endcan
                </div>

                <!-- Stats summary bar -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-8 border-t border-gray-700/60">
                    <div>
                        <div class="text-2xl font-black text-cyan-400">{{ $stats['active_tournaments_count'] }}</div>
                        <div class="text-xs text-gray-400">Active Tournaments</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-sky-400">{{ $stats['teams_count'] }}</div>
                        <div class="text-xs text-gray-400">Registered Teams</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">{{ $stats['players_count'] }}</div>
                        <div class="text-xs text-gray-400">Active Players</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-cyan-300">{{ $stats['matches_count'] }}</div>
                        <div class="text-xs text-gray-400">Fixtures Logged</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-Time Live Score Ticker Section (Alpine + Polling / Echo) -->
        <section x-data="liveTicker()" x-init="initTicker()" class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        Live Match Radar & Ticker
                    </h2>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Live broadcast feed active</span>
                    <button @click="refreshTicker()" class="text-cyan-400 hover:text-cyan-300 ml-2" title="Manual refresh">
                        <svg class="w-4 h-4 inline" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Horizontal Scrollable Live Ticker Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="match in matches" :key="match.id">
                    <div class="bg-gray-800/95 border border-gray-700/80 hover:border-cyan-500/50 rounded-xl p-3.5 shadow-lg relative overflow-hidden transition">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-bold text-gray-400 truncate max-w-[150px]" x-text="match.tournament"></span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                                :class="match.status === 'live' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30 animate-pulse' : (match.status === 'upcoming' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/30' : 'bg-gray-700 text-gray-300')"
                                x-text="match.status === 'live' ? '● LIVE' : match.stage"></span>
                        </div>

                        <div class="flex items-center justify-between my-2">
                            <div class="font-bold text-sm text-white truncate max-w-[110px]" x-text="match.home_team"></div>
                            <div class="px-3 py-1 bg-gray-900 rounded-lg border border-gray-700 font-black text-cyan-400 text-sm tracking-wider">
                                <span x-text="match.home_score"></span> - <span x-text="match.away_score"></span>
                            </div>
                            <div class="font-bold text-sm text-white truncate max-w-[110px] text-right" x-text="match.away_team"></div>
                        </div>

                        <div class="text-[11px] text-gray-400 mt-2 flex items-center justify-between pt-2 border-t border-gray-700/50">
                            <span class="truncate" x-text="match.venue"></span>
                            <span class="text-cyan-400 font-mono" x-text="match.scheduled_at"></span>
                        </div>
                    </div>
                </template>

                @if(empty($tickerData))
                    <div class="col-span-3 text-center py-6 bg-gray-800/50 rounded-xl border border-gray-700 text-gray-400 text-xs">
                        No matches currently scheduled. Explore or generate fixtures in Tournaments.
                    </div>
                @endif
            </div>
        </section>

        <!-- Upcoming Fixtures Section with Interactive Countdown -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        Upcoming Fixtures
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Next scheduled tournament clashes and exhibition friendlies</p>
                </div>
                <a href="{{ route('tournaments.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold flex items-center gap-1">
                    <span>View All</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($upcomingFixtures as $match)
                    <x-match-card :match="$match" />
                @empty
                    <div class="col-span-3 text-center py-8 bg-gray-800/50 rounded-xl border border-gray-700 text-gray-400 text-xs">
                        No upcoming fixtures found.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Recent Results Section -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        Recent Match Results
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Completed official games and verified scorelines</p>
                </div>
                <a href="{{ route('news.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold flex items-center gap-1">
                    <span>Match Reports</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($recentResults as $match)
                    <x-match-card :match="$match" />
                @empty
                    <div class="col-span-4 text-center py-8 bg-gray-800/50 rounded-xl border border-gray-700 text-gray-400 text-xs">
                        No completed matches yet.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Active Tournaments Spotlight & Latest News -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Tournaments -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Active Tournaments</h2>
                    <a href="{{ route('tournaments.index') }}" class="text-xs text-cyan-400 hover:underline">See All ({{ $stats['tournaments_count'] }})</a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($featuredTournaments as $tournament)
                        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 hover:border-cyan-500/50 transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                        {{ $tournament->sport_type }}
                                    </span>
                                    <span class="text-xs font-medium text-gray-400 uppercase">
                                        Stage: {{ ucfirst(str_replace('_', ' ', $tournament->current_stage)) }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-white mb-2">
                                    <a href="{{ route('tournaments.show', $tournament->slug) }}" class="hover:text-cyan-400 transition">
                                        {{ $tournament->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-gray-400 line-clamp-2 mb-4">
                                    {{ $tournament->description }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-gray-700/60 flex items-center justify-between text-xs text-gray-400">
                                <div>
                                    <span class="text-white font-bold">{{ $tournament->teams_count }}</span> Teams Registered
                                </div>
                                <a href="{{ route('tournaments.show', $tournament->slug) }}" class="text-cyan-400 font-semibold hover:underline">
                                    Bracket Tree &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right 1 Col: Latest News & Disciplinary Feed -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">League Dispatch</h2>
                    <a href="{{ route('news.index') }}" class="text-xs text-cyan-400 hover:underline">All News</a>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-xl divide-y divide-gray-700">
                    @forelse($latestNews as $article)
                        <div class="p-4 hover:bg-gray-750 transition">
                            <div class="flex items-center gap-2 mb-1">
                                @if($article->match_incident_flag)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                        INCIDENT
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                        ANNOUNCEMENT
                                    </span>
                                @endif
                                <span class="text-[11px] text-gray-500">{{ $article->published_at ? $article->published_at->diffForHumans() : 'Recent' }}</span>
                            </div>
                            <a href="{{ route('news.show', $article->slug) }}" class="text-sm font-semibold text-white hover:text-cyan-400 transition block">
                                {{ $article->title }}
                            </a>
                            <p class="text-xs text-gray-400 line-clamp-2 mt-1">
                                {{ $article->excerpt }}
                            </p>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-gray-400">
                            No news announcements currently.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Create Tournament Modal (For League Admin) -->
    @can('create-tournament')
    <div id="create-tournament-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-700">
                    <h3 class="text-lg font-bold text-white">Create Multi-Sport Tournament</h3>
                    <button type="button" class="text-gray-400 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" data-modal-hide="create-tournament-modal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>

                <form action="{{ route('tournaments.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="title" class="block mb-1 text-xs font-medium text-gray-300">Tournament Title</label>
                        <input type="text" name="title" id="title" required class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5" placeholder="Champions League 2026">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="sport_type" class="block mb-1 text-xs font-medium text-gray-300">Sport Discipline</label>
                            <select name="sport_type" id="sport_type" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                                <option value="Football">Football / Soccer</option>
                                <option value="Basketball">Basketball</option>
                                <option value="Esports">Esports (Valorant / CS2)</option>
                                <option value="Cricket">Cricket T20</option>
                            </select>
                        </div>
                        <div>
                            <label for="max_teams" class="block mb-1 text-xs font-medium text-gray-300">Max Teams Capacity</label>
                            <select name="max_teams" id="max_teams" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                                <option value="8">8 Teams (Quarterfinals)</option>
                                <option value="4">4 Teams (Semifinals)</option>
                                <option value="16">16 Teams (Round of 16)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="venue" class="block mb-1 text-xs font-medium text-gray-300">Host Venue / Server</label>
                            <input type="text" name="venue" id="venue" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5" placeholder="National Olympic Arena">
                        </div>
                        <div>
                            <label for="prize_pool" class="block mb-1 text-xs font-medium text-gray-300">Prize Pool</label>
                            <input type="text" name="prize_pool" id="prize_pool" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5" placeholder="$50,000 USD">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block mb-1 text-xs font-medium text-gray-300">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5">
                        </div>
                        <div>
                            <label for="end_date" class="block mb-1 text-xs font-medium text-gray-300">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block mb-1 text-xs font-medium text-gray-300">Rules & Description</label>
                        <textarea name="description" id="description" rows="3" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5" placeholder="Tournament format, eligibility, and stage guidelines..."></textarea>
                    </div>

                    <button type="submit" class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition">
                        Launch Tournament & Open Registration
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @push('scripts')
    <script>
        function liveTicker() {
            return {
                matches: @json($tickerData),
                loading: false,
                initTicker() {
                    // Poll live match data every 8 seconds
                    setInterval(() => {
                        this.refreshTicker();
                    }, 8000);
                },
                async refreshTicker() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('api.matches.ticker') }}');
                        if (res.ok) {
                            const data = await res.json();
                            if (data.matches && data.matches.length > 0) {
                                this.matches = data.matches;
                            }
                        }
                    } catch (e) {
                        console.error('Ticker update error:', e);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
