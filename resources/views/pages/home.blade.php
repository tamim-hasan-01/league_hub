<x-app-layout>
    <div class="space-y-10">
        <section class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-slate-950 px-6 py-10 text-white shadow-[0_24px_60px_rgba(15,23,42,0.12)] sm:px-10 lg:px-12 lg:py-14">
            <div class="absolute right-0 top-0 h-full w-2/5 bg-[radial-gradient(circle_at_center,rgba(129,140,248,0.24),transparent_65%)]"></div>
            <div class="relative grid items-end gap-10 lg:grid-cols-[1.45fr_0.55fr]">
                <div class="max-w-3xl">
                    <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-indigo-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Competition workspace
                    </p>
                    <h1 class="text-4xl font-black leading-[1.05] tracking-tight sm:text-5xl lg:text-6xl">
                        Every match.<br>
                        <span class="text-indigo-300">Every moment.</span>
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-7 text-slate-300">
                        Run tournaments, follow live results, manage team rosters, and keep your whole competition community aligned from one clear workspace.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('tournaments.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-950 shadow-sm hover:-translate-y-0.5 hover:bg-indigo-50">
                            Explore tournaments
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="{{ route('teams.index') }}" class="inline-flex items-center rounded-xl border border-white/15 px-5 py-3 text-sm font-bold text-white hover:border-white/30 hover:bg-white/5">
                            Browse teams
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/10">
                    @foreach([
                        ['active tournaments', $stats['active_tournaments_count']],
                        ['registered teams', $stats['teams_count']],
                        ['active players', $stats['players_count']],
                        ['matches logged', $stats['matches_count']],
                    ] as [$label, $value])
                        <div class="bg-slate-950/70 p-5">
                            <p class="text-3xl font-black tracking-tight">{{ $value }}</p>
                            <p class="mt-1 text-[11px] font-bold uppercase tracking-wider text-slate-200">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section x-data="liveTicker()" x-init="initTicker()" class="space-y-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Live center</p>
                    <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Match ticker</h2>
                </div>
                <button @click="refreshTicker()" type="button" class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-xs font-bold text-slate-600 shadow-sm hover:border-indigo-200 hover:text-indigo-600">
                    <svg class="h-4 w-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </button>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <template x-for="match in matches" :key="match.id">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3 text-xs">
                            <span class="truncate font-bold text-slate-500" x-text="match.tournament"></span>
                            <span class="rounded-full px-2 py-1 text-[10px] font-black uppercase tracking-wider"
                                  :class="match.status === 'live' ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-600'"
                                  x-text="match.status === 'live' ? 'Live' : match.stage"></span>
                        </div>
                        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 py-5 text-sm">
                            <span class="font-bold text-slate-900" x-text="match.home_team"></span>
                            <span class="rounded-lg bg-slate-950 px-3 py-2 font-black text-white">
                                <span x-text="match.home_score"></span> : <span x-text="match.away_score"></span>
                            </span>
                            <span class="text-right font-bold text-slate-900" x-text="match.away_team"></span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-3 text-xs text-slate-500">
                            <span class="truncate" x-text="match.venue"></span>
                            <span class="shrink-0 font-bold text-indigo-600" x-text="match.scheduled_at"></span>
                        </div>
                    </article>
                </template>
                @if(empty($tickerData))
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white py-10 text-center text-sm text-slate-500">No matches are scheduled right now.</div>
                @endif
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Coming up</p>
                    <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Upcoming fixtures</h2>
                </div>
                <a href="{{ route('tournaments.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">View tournaments &rarr;</a>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse($upcomingFixtures as $match)
                    <x-match-card :match="$match" />
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white py-10 text-center text-sm text-slate-500">No upcoming fixtures.</div>
                @endforelse
            </div>
        </section>

        <div class="grid gap-8 lg:grid-cols-[1.45fr_0.55fr]">
            <section class="space-y-4">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Tournaments</p>
                        <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Active competitions</h2>
                    </div>
                    <a href="{{ route('tournaments.index') }}" class="text-sm font-bold text-indigo-600">View all</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @forelse($featuredTournaments as $tournament)
                        <article class="flex min-h-52 flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $tournament->sport_type }}</span>
                                    <span class="text-xs font-bold text-slate-400">{{ str_replace('_', ' ', $tournament->current_stage) }}</span>
                                </div>
                                <h3 class="mt-4 text-lg font-black text-slate-950">
                                    <a href="{{ route('tournaments.show', $tournament->slug) }}" class="hover:text-indigo-600">{{ $tournament->title }}</a>
                                </h3>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-500">{{ $tournament->description }}</p>
                            </div>
                            <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs">
                                <span class="text-slate-500"><strong class="text-slate-950">{{ $tournament->teams_count }}</strong> teams</span>
                                <a href="{{ route('tournaments.show', $tournament->slug) }}" class="font-bold text-indigo-600">View bracket &rarr;</a>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white py-10 text-center text-sm text-slate-500">No active tournaments.</div>
                    @endforelse
                </div>
            </section>

            <aside class="space-y-4">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Latest</p>
                        <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950">League news</h2>
                    </div>
                    <a href="{{ route('news.index') }}" class="text-sm font-bold text-indigo-600">View all</a>
                </div>
                <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                    @forelse($latestNews as $article)
                        <article class="p-5 hover:bg-slate-50">
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <span class="text-[10px] font-black uppercase tracking-wider {{ $article->match_incident_flag ? 'text-rose-600' : 'text-indigo-600' }}">
                                    {{ $article->match_incident_flag ? 'Incident' : 'Announcement' }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $article->published_at ? $article->published_at->diffForHumans() : 'Recent' }}</span>
                            </div>
                            <h3 class="font-bold text-slate-950">
                                <a href="{{ route('news.show', $article->slug) }}" class="hover:text-indigo-600">{{ $article->title }}</a>
                            </h3>
                            <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-500">{{ $article->excerpt }}</p>
                        </article>
                    @empty
                        <p class="p-8 text-center text-sm text-slate-500">No news published yet.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </div>

    @push('scripts')
        <script>
            function liveTicker() {
                return {
                    matches: @json($tickerData),
                    loading: false,
                    initTicker() {
                        setInterval(() => this.refreshTicker(), 8000);
                    },
                    async refreshTicker() {
                        this.loading = true;
                        try {
                            const response = await fetch('{{ route('api.matches.ticker') }}');
                            if (response.ok) {
                                const data = await response.json();
                                if (data.matches) {
                                    this.matches = data.matches;
                                }
                            }
                        } finally {
                            this.loading = false;
                        }
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
