<x-app-layout>
    <div class="space-y-8">
        <section class="flex flex-col gap-5 border-b border-slate-200 pb-7 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold text-indigo-600">League newsroom</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">News and incidents</h1>
                <p class="mt-2 max-w-2xl text-base leading-7 text-slate-500">
                    Official announcements, match reports, disciplinary updates, and league statements.
                </p>
            </div>
            @can('create-tournament')
                <button type="button" data-modal-target="publish-article-modal" data-modal-toggle="publish-article-modal"
                        class="inline-flex h-12 items-center justify-center rounded-xl bg-slate-950 px-5 text-sm font-bold text-white shadow-sm hover:bg-indigo-600">
                    Publish report
                </button>
            @endcan
        </section>

        <nav class="flex flex-wrap gap-2" aria-label="News filters">
            <a href="{{ route('news.index') }}"
               class="rounded-lg px-4 py-2.5 text-sm font-bold {{ !request('filter') ? 'bg-slate-950 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-indigo-200 hover:text-indigo-600' }}">
                All reports
            </a>
            <a href="{{ route('news.index', ['filter' => 'incidents']) }}"
               class="rounded-lg px-4 py-2.5 text-sm font-bold {{ request('filter') === 'incidents' ? 'bg-rose-600 text-white' : 'border border-slate-200 bg-white text-rose-700 hover:border-rose-200' }}">
                Disciplinary incidents
            </a>
            <a href="{{ route('news.index', ['filter' => 'announcements']) }}"
               class="rounded-lg px-4 py-2.5 text-sm font-bold {{ request('filter') === 'announcements' ? 'bg-indigo-600 text-white' : 'border border-slate-200 bg-white text-indigo-700 hover:border-indigo-200' }}">
                Announcements
            </a>
        </nav>

        <div class="grid gap-5 lg:grid-cols-2">
            @forelse($articles as $article)
                <article class="group flex min-h-72 flex-col justify-between rounded-2xl border {{ $article->match_incident_flag ? 'border-rose-200' : 'border-slate-200' }} bg-white p-6 shadow-[0_8px_24px_rgba(15,23,42,0.05)] transition hover:-translate-y-0.5 hover:border-indigo-200">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            @if($article->match_incident_flag)
                                <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    Disciplinary incident
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                    League announcement
                                </span>
                            @endif
                            <time class="shrink-0 text-xs font-semibold text-slate-400">
                                {{ $article->published_at ? $article->published_at->format('M d, Y') : 'Recent' }}
                            </time>
                        </div>

                        <h2 class="mt-5 text-xl font-black leading-7 text-slate-950">
                            <a href="{{ route('news.show', $article->slug) }}" class="hover:text-indigo-600">{{ $article->title }}</a>
                        </h2>
                        <p class="mt-3 line-clamp-3 text-sm leading-7 text-slate-500">{{ $article->excerpt }}</p>

                        @if($article->match)
                            <div class="mt-5 flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Related fixture</p>
                                    <p class="mt-1 truncate text-sm font-bold text-slate-800">
                                        {{ $article->match->homeTeam->name }} vs {{ $article->match->awayTeam->name }}
                                    </p>
                                </div>
                                @if($article->match->status !== 'upcoming')
                                    <span class="shrink-0 rounded-lg bg-slate-950 px-2.5 py-1.5 text-xs font-black tabular-nums text-white">
                                        {{ $article->match->home_score }} : {{ $article->match->away_score }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                        <p class="truncate text-xs text-slate-400">By {{ $article->author->name ?? 'League authority' }}</p>
                        <a href="{{ route('news.show', $article->slug) }}" class="shrink-0 text-sm font-bold text-indigo-600 hover:text-indigo-800">
                            Read report &rarr;
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-16 text-center">
                    <p class="text-base font-bold text-slate-700">No reports found</p>
                    <p class="mt-1 text-sm text-slate-500">Try another newsroom filter.</p>
                </div>
            @endforelse
        </div>

        <div>{{ $articles->links() }}</div>
    </div>

    @can('create-tournament')
        <div id="publish-article-modal" tabindex="-1" aria-hidden="true"
             class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto md:inset-0">
            <div class="relative w-full max-w-xl p-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-indigo-600">Newsroom</p>
                            <h2 class="mt-1 text-lg font-black text-slate-950">Publish a report</h2>
                        </div>
                        <button type="button" data-modal-hide="publish-article-modal" aria-label="Close report publisher"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">&times;</button>
                    </div>
                    <form action="{{ route('news.store') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="title" class="mb-2 block text-sm font-bold text-slate-700">Headline</label>
                            <input type="text" name="title" id="title" required
                                   class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900"
                                   placeholder="Disciplinary hearing: match conduct review">
                        </div>
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <input type="checkbox" name="match_incident_flag" value="1" class="h-4 w-4 rounded border-slate-300 text-rose-600">
                            <span class="text-sm font-bold text-rose-700">Flag as a disciplinary incident</span>
                        </label>
                        <div>
                            <label for="match_id" class="mb-2 block text-sm font-bold text-slate-700">Related fixture</label>
                            <select name="match_id" id="match_id" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900">
                                <option value="">General announcement</option>
                                @foreach($recentMatches as $match)
                                    <option value="{{ $match->id }}">{{ $match->homeTeam->name }} vs {{ $match->awayTeam->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="excerpt" class="mb-2 block text-sm font-bold text-slate-700">Summary</label>
                            <input type="text" name="excerpt" id="excerpt" class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900"
                                   placeholder="Short public summary">
                        </div>
                        <div>
                            <label for="body" class="mb-2 block text-sm font-bold text-slate-700">Full report</label>
                            <textarea name="body" id="body" rows="5" required
                                      class="w-full rounded-xl border border-slate-200 px-3 py-3 text-sm leading-6 text-slate-900"
                                      placeholder="Official details and statements"></textarea>
                        </div>
                        <button type="submit" class="h-12 w-full rounded-xl bg-slate-950 text-sm font-bold text-white hover:bg-indigo-600">Publish report</button>
                    </form>
                </div>
            </div>
        </div>
    @endcan
</x-app-layout>
