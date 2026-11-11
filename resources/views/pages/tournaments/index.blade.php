<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-col gap-5 border-b border-slate-200 pb-7 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Competition directory</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Tournaments</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $activeCount }} active competitions across every discipline</p>
            </div>
            @can('create-tournament')
                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-indigo-600">
                    Create tournament
                </button>
            @endcan
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tournaments.index') }}" class="rounded-lg px-3 py-2 text-xs font-bold {{ !request('sport') ? 'bg-slate-950 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-indigo-200 hover:text-indigo-600' }}">All sports</a>
            @foreach($sports as $sport)
                <a href="{{ route('tournaments.index', ['sport' => $sport]) }}" class="rounded-lg px-3 py-2 text-xs font-bold {{ request('sport') === $sport ? 'bg-slate-950 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:border-indigo-200 hover:text-indigo-600' }}">{{ $sport }}</a>
            @endforeach
        </div>

        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($tournaments as $tournament)
                <article class="flex min-h-72 flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $tournament->sport_type }}</span>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">{{ str_replace('_', ' ', $tournament->status) }}</span>
                        </div>
                        <h2 class="mt-5 text-xl font-black text-slate-950">
                            <a href="{{ route('tournaments.show', $tournament->slug) }}" class="hover:text-indigo-600">{{ $tournament->title }}</a>
                        </h2>
                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500">{{ $tournament->description }}</p>
                        <div class="mt-5 grid grid-cols-2 gap-px overflow-hidden rounded-xl border border-slate-200 bg-slate-200">
                            <div class="min-h-20 bg-slate-50 px-3 py-3">
                                <span class="block text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Teams</span>
                                <strong class="mt-2 block text-base font-extrabold tabular-nums text-slate-950">{{ $tournament->teams_count }} / {{ $tournament->max_teams }}</strong>
                            </div>
                            <div class="min-h-20 bg-slate-50 px-3 py-3">
                                <span class="block text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Prize pool</span>
                                <strong class="mt-2 block text-sm font-extrabold leading-5 text-slate-950">{{ $tournament->prize_pool ?? 'Trophy' }}</strong>
                            </div>
                            <div class="min-h-20 bg-slate-50 px-3 py-3">
                                <span class="block text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Starts</span>
                                <strong class="mt-2 block text-sm font-extrabold leading-5 text-slate-950">{{ $tournament->start_date ? $tournament->start_date->format('M d, Y') : 'TBA' }}</strong>
                            </div>
                            <div class="min-h-20 bg-slate-50 px-3 py-3">
                                <span class="block text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Phase</span>
                                <strong class="mt-2 block text-sm font-extrabold leading-5 capitalize text-slate-950">{{ str_replace('_', ' ', $tournament->current_stage) }}</strong>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('tournaments.show', $tournament->slug) }}" class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-sm font-bold text-indigo-600">
                        View tournament <span>&rarr;</span>
                    </a>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center text-sm text-slate-500">No tournaments match this filter.</div>
            @endforelse
        </div>
        <div>{{ $tournaments->links() }}</div>
    </div>
</x-app-layout>
