<x-app-layout>
    <div class="space-y-8">
        <section class="flex flex-col gap-5 border-b border-slate-200 pb-7 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Club directory</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Teams</h1>
                <p class="mt-2 text-base text-slate-500">Explore rosters, managers, venues, and competitive records.</p>
            </div>
            @auth
                @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                    <a href="{{ route('teams.create') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-slate-950 px-5 text-sm font-bold text-white shadow-sm hover:bg-indigo-600">
                        Create team
                    </a>
                @endif
            @endauth
        </section>

        <form action="{{ route('teams.index') }}" method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-3 sm:grid-cols-[minmax(0,1fr)_180px_auto]">
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search team name or venue"
                       class="h-12 w-full rounded-xl border-0 bg-slate-50 pl-11 pr-3 text-sm font-medium text-slate-900 placeholder:text-slate-400">
            </div>
            <select name="sport" onchange="this.form.submit()" class="h-12 rounded-xl border border-slate-200 bg-white px-3 text-sm font-bold text-slate-700">
                <option value="">All sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport }}" {{ request('sport') === $sport ? 'selected' : '' }}>{{ $sport }}</option>
                @endforeach
            </select>
            <button type="submit" class="h-12 rounded-xl bg-slate-950 px-6 text-sm font-bold text-white hover:bg-indigo-600">Search</button>
        </form>

        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($teams as $team)
                @php $record = $team->record; @endphp
                <article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)] transition hover:-translate-y-0.5 hover:border-indigo-200">
                    <div class="flex items-center justify-between gap-3">
                        <span class="rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $team->sport_type }}</span>
                        <span class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-black text-slate-600">Rank #{{ $team->areal_ranking }}</span>
                    </div>

                    <div class="mt-5 flex items-center gap-4">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-lg font-black text-white shadow-sm">
                            {{ strtoupper(substr($team->name, 0, 2)) }}
                        </span>
                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-black text-slate-950">
                                <a href="{{ route('teams.show', $team->slug) }}" class="hover:text-indigo-600">{{ $team->name }}</a>
                            </h2>
                            <p class="mt-1 truncate text-sm text-slate-500">{{ $team->manager->name ?? 'League staff' }}</p>
                        </div>
                    </div>

                    <p class="mt-5 line-clamp-2 min-h-12 text-sm leading-6 text-slate-500">
                        {{ $team->description ?: 'Competitive franchise representing the regional league.' }}
                    </p>

                    <div class="mt-5 grid grid-cols-3 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                        <div class="px-3 py-3 text-center">
                            <p class="text-[11px] font-bold uppercase tracking-[0.10em] text-slate-400">Wins</p>
                            <p class="mt-1 text-xl font-black text-emerald-700">{{ $record['wins'] }}</p>
                        </div>
                        <div class="border-x border-slate-200 px-3 py-3 text-center">
                            <p class="text-[11px] font-bold uppercase tracking-[0.10em] text-slate-400">Losses</p>
                            <p class="mt-1 text-xl font-black text-rose-700">{{ $record['losses'] }}</p>
                        </div>
                        <div class="px-3 py-3 text-center">
                            <p class="text-[11px] font-bold uppercase tracking-[0.10em] text-slate-400">Draws</p>
                            <p class="mt-1 text-xl font-black text-slate-700">{{ $record['draws'] }}</p>
                        </div>
                    </div>

                    <a href="{{ route('teams.show', $team->slug) }}" class="mt-5 flex h-11 items-center justify-between border-t border-slate-100 pt-4 text-sm font-bold text-indigo-600">
                        View team profile <span class="text-lg">&rarr;</span>
                    </a>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-16 text-center">
                    <p class="text-base font-bold text-slate-700">No teams match your search</p>
                    <p class="mt-1 text-sm text-slate-500">Try another sport or team name.</p>
                </div>
            @endforelse
        </div>
        <div>{{ $teams->links() }}</div>
    </div>
</x-app-layout>
