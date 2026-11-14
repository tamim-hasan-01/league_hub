<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-8">
        <article class="overflow-hidden rounded-[28px] border {{ $article->match_incident_flag ? 'border-rose-200' : 'border-slate-200' }} bg-white shadow-[0_16px_40px_rgba(15,23,42,0.07)]">
            <header class="border-b border-slate-100 px-6 py-7 sm:px-10 sm:py-9">
                <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 hover:text-indigo-800">
                    <span aria-hidden="true">&larr;</span> All reports
                </a>

                <div class="mt-6 flex flex-wrap items-center gap-2">
                    @if($article->match_incident_flag)
                        <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                            Official disciplinary incident
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">
                            League announcement
                        </span>
                    @endif
                </div>

                <h1 class="mt-5 max-w-4xl text-3xl font-black leading-[1.12] tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">
                    {{ $article->title }}
                </h1>

                @if($article->excerpt)
                    <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-600">
                        {{ $article->excerpt }}
                    </p>
                @endif

                <div class="mt-7 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-slate-100 pt-5 text-sm text-slate-500">
                    <span><strong class="text-slate-800">Published</strong> {{ $article->published_at ? $article->published_at->format('M d, Y · H:i') : 'Recently' }}</span>
                    <span><strong class="text-slate-800">Author</strong> {{ $article->author->name ?? 'League authority' }}</span>
                </div>
            </header>

            @if($article->match)
                <aside class="mx-6 mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:mx-10">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Related fixture</p>
                            <p class="mt-2 text-lg font-black text-slate-950">
                                {{ $article->match->homeTeam->name }} <span class="mx-2 text-slate-400">vs</span> {{ $article->match->awayTeam->name }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            @if($article->match->status !== 'upcoming')
                                <span class="flex h-12 min-w-20 items-center justify-center rounded-xl bg-slate-950 px-3 text-lg font-black tabular-nums text-white">
                                    {{ $article->match->home_score }} : {{ $article->match->away_score }}
                                </span>
                            @endif
                            <span class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold capitalize text-slate-600">
                                {{ str_replace('_', ' ', $article->match->stage) }}
                            </span>
                        </div>
                    </div>
                </aside>
            @endif

            <div class="px-6 py-8 sm:px-10 sm:py-10">
                <div class="max-w-3xl whitespace-pre-line text-base leading-8 text-slate-700">
                    {{ $article->body }}
                </div>
            </div>
        </article>

        @if($relatedArticles->isNotEmpty())
            <section class="space-y-4">
                <div>
                    <p class="text-sm font-semibold text-indigo-600">Continue reading</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Related reports</h2>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach($relatedArticles as $related)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                            <time class="text-xs font-bold text-slate-400">
                                {{ $related->published_at ? $related->published_at->format('M d, Y') : 'Recent' }}
                            </time>
                            <h3 class="mt-3 line-clamp-3 text-base font-black leading-6 text-slate-950">
                                <a href="{{ route('news.show', $related->slug) }}" class="hover:text-indigo-600">{{ $related->title }}</a>
                            </h3>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
