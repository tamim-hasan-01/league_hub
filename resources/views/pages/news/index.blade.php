<x-app-layout>
    <div class="space-y-8">
        <!-- Header & Filter -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-800">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-white">News & Match Incidents</h1>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Official league press dispatches, executive statements, and disciplinary referee incident reviews</p>
            </div>

            @can('create-tournament')
                <button data-modal-target="publish-article-modal" data-modal-toggle="publish-article-modal" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-gray-950 shadow-md shadow-cyan-500/20 transition flex items-center gap-1.5 self-start md:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Publish Announcement / Incident</span>
                </button>
            @endcan
        </div>

        <!-- Filter Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('news.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('filter') ? 'bg-cyan-500 text-gray-950 font-bold' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700' }}">
                All Dispatches
            </a>
            <a href="{{ route('news.index', ['filter' => 'incidents']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request('filter') === 'incidents' ? 'bg-amber-500 text-gray-950 font-bold' : 'bg-gray-800 text-amber-400 hover:bg-gray-700' }}">
                ⚠️ Disciplinary Incidents Only
            </a>
            <a href="{{ route('news.index', ['filter' => 'announcements']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request('filter') === 'announcements' ? 'bg-cyan-500 text-gray-950 font-bold' : 'bg-gray-800 text-cyan-400 hover:bg-gray-700' }}">
                📢 League Announcements
            </a>
        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($articles as $article)
                <div class="bg-gray-800 border {{ $article->match_incident_flag ? 'border-amber-500/40' : 'border-gray-700' }} hover:border-cyan-500/50 rounded-2xl p-6 shadow-xl transition flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            @if($article->match_incident_flag)
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    DISCIPLINARY INCIDENT LOG
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-cyan-500/15 text-cyan-400 border border-cyan-500/30">
                                    LEAGUE ANNOUNCEMENT
                                </span>
                            @endif

                            <span class="text-xs text-gray-500">
                                {{ $article->published_at ? $article->published_at->format('M d, Y') : 'Recent' }}
                            </span>
                        </div>

                        <h2 class="text-lg font-bold text-white group-hover:text-cyan-400 transition mb-2">
                            <a href="{{ route('news.show', $article->slug) }}">
                                {{ $article->title }}
                            </a>
                        </h2>

                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed mb-4">
                            {{ $article->excerpt }}
                        </p>

                        @if($article->match)
                            <div class="p-2.5 bg-gray-900/60 rounded-xl border border-gray-700/60 text-xs text-gray-400 mb-4 flex items-center justify-between">
                                <span>Related Fixture:</span>
                                <span class="text-white font-bold">{{ $article->match->homeTeam->name }} vs {{ $article->match->awayTeam->name }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-gray-700/60 flex items-center justify-between text-xs text-gray-400">
                        <span>Reporter: {{ $article->author->name ?? 'League Commissioner' }}</span>
                        <a href="{{ route('news.show', $article->slug) }}" class="text-cyan-400 font-semibold hover:underline flex items-center gap-1">
                            <span>Read Full Report</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-16 bg-gray-800/40 rounded-2xl border border-gray-700 text-gray-400 text-sm">
                    No reports found for this filter.
                </div>
            @endforelse
        </div>

        <div>
            {{ $articles->links() }}
        </div>
    </div>

    <!-- Admin Publish Article / Incident Modal -->
    @can('create-tournament')
    <div id="publish-article-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-700">
                    <h3 class="text-base font-bold text-white">Publish League Report / Incident</h3>
                    <button type="button" class="text-gray-400 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" data-modal-hide="publish-article-modal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>

                <form action="{{ route('news.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="title" class="block mb-1 text-xs font-medium text-gray-300">Article Title / Headline</label>
                        <input type="text" name="title" id="title" required class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5" placeholder="Disciplinary Hearing: Match #4 Conduct Review">
                    </div>

                    <div>
                        <label class="cursor-pointer flex items-center gap-2 p-2.5 bg-gray-900 rounded-lg border border-gray-700">
                            <input type="checkbox" name="match_incident_flag" value="1" class="w-4 h-4 text-cyan-500 rounded bg-gray-800 border-gray-600">
                            <span class="text-xs font-bold text-amber-400">Flag as Disciplinary Match Incident Report</span>
                        </label>
                    </div>

                    <div>
                        <label for="match_id" class="block mb-1 text-xs font-medium text-gray-300">Related Fixture (Optional)</label>
                        <select name="match_id" id="match_id" class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg block w-full p-2.5">
                            <option value="">None / General Announcement</option>
                            @foreach($recentMatches as $m)
                                <option value="{{ $m->id }}">{{ $m->homeTeam->name }} vs {{ $m->awayTeam->name }} ({{ $m->scheduled_at->format('M d') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="excerpt" class="block mb-1 text-xs font-medium text-gray-300">Short Summary Excerpt</label>
                        <input type="text" name="excerpt" id="excerpt" class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg block w-full p-2.5" placeholder="Brief summary of disciplinary sanctions or announcement...">
                    </div>

                    <div>
                        <label for="body" class="block mb-1 text-xs font-medium text-gray-300">Full Report Body</label>
                        <textarea name="body" id="body" rows="4" required class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5" placeholder="Full details, official statements, and referee citations..."></textarea>
                    </div>

                    <button type="submit" class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition">
                        Broadcast News Dispatch
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan
</x-app-layout>

