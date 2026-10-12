<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        <div class="bg-gray-800 border {{ $article->match_incident_flag ? 'border-amber-500/40' : 'border-gray-700' }} rounded-3xl p-6 sm:p-10 shadow-2xl relative overflow-hidden">
            @if($article->match_incident_flag)
                <div class="absolute top-0 left-0 right-0 h-1 bg-amber-500"></div>
            @else
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 to-sky-400"></div>
            @endif

            <div class="mb-6">
                <a href="{{ route('news.index') }}" class="text-xs text-gray-400 hover:text-cyan-400 flex items-center gap-1 mb-4">
                    &larr; Back to News Dispatches
                </a>

                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if($article->match_incident_flag)
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center gap-1">
                            ⚠️ OFFICIAL DISCIPLINARY INCIDENT LOG
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-cyan-500/15 text-cyan-400 border border-cyan-500/30">
                            LEAGUE EXECUTIVE ANNOUNCEMENT
                        </span>
                    @endif

                    <span class="text-xs text-gray-400">
                        Published {{ $article->published_at ? $article->published_at->format('M d, Y - H:i') : 'Recent' }}
                    </span>
                    <span class="text-xs text-gray-500">&bull; Authored by {{ $article->author->name ?? 'League Authority' }}</span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight mb-4">
                    {{ $article->title }}
                </h1>

                @if($article->excerpt)
                    <p class="text-base sm:text-lg text-gray-300 font-medium pb-4 border-b border-gray-700/80">
                        {{ $article->excerpt }}
                    </p>
                @endif
            </div>

            @if($article->match)
                <!-- Associated Match Widget -->
                <div class="my-6 p-4 rounded-2xl bg-gray-900/80 border border-gray-700/80">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Subject Match Fixture</div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="font-bold text-white">
                            {{ $article->match->homeTeam->name }} <span class="text-cyan-400">{{ $article->match->home_score }} : {{ $article->match->away_score }}</span> {{ $article->match->awayTeam->name }}
                        </div>
                        <div class="text-xs text-gray-400">
                            Stage: {{ ucfirst(str_replace('_', ' ', $article->match->stage)) }} &bull; {{ $article->match->venue }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Body Content -->
            <div class="text-sm sm:text-base text-gray-200 leading-relaxed space-y-4 whitespace-pre-line">
                {{ $article->body }}
            </div>
        </div>

        <!-- Related Dispatches -->
        @if($relatedArticles->isNotEmpty())
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-white">Related Dispatches</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($relatedArticles as $rel)
                        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                            <span class="text-[10px] text-gray-400 block mb-1">{{ $rel->published_at ? $rel->published_at->format('M d') : 'Recent' }}</span>
                            <a href="{{ route('news.show', $rel->slug) }}" class="text-xs font-bold text-white hover:text-cyan-400 line-clamp-2">
                                {{ $rel->title }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

