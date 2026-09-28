<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-col gap-5 border-b border-slate-200 pb-7 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Club directory</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Teams</h1>
                <p class="mt-2 text-sm text-slate-500">Explore rosters, managers, and competitive records</p>
            </div>
            @auth
                @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                    <a href="{{ route('teams.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white hover:bg-indigo-600">Create team</a>
                @endif
            @endauth
        </div>

        <form action="{{ route('teams.index') }}" method="GET" class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-3 sm:flex-row">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search teams or home arenas" class="w-full rounded-xl border-0 bg-slate-50 py-3 pl-10 pr-3 text-sm text-slate-900 placeholder:text-slate-400">
            </div>
            <select name="sport" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm font-semibold text-slate-700">
                <option value="">All sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport }}" {{ request('sport') === $sport ? 'selected' : '' }}>{{ $sport }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-600">Search</button>
        </form>

        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($teams as $team)
                @php $record = $team->record; @endphp
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                    <div class="flex items-start justify-between gap-3">
                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $team->sport_type }}</span>
                        <span class="text-xs font-black text-slate-400">Rank #{{ $team->areal_ranking }}</span>
                    </div>
                    <div class="mt-5 flex items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-950 text-sm font-black text-white">{{ substr($team->name, 0, 2) }}</span>
                        <div class="min-w-0">
                            <h2 class="truncate text-lg font-black text-slate-950">
                                <a href="{{ route('teams.show', $team->slug) }}" class="hover:text-indigo-600">{{ $team->name }}</a>
                            </h2>
                            <p class="truncate text-xs text-slate-500">Manager: {{ $team->manager->name ?? 'Staff' }}</p>
                        </div>
                    </div>
                    <p class="mt-4 line-clamp-2 min-h-12 text-sm leading-6 text-slate-500">{{ $team->description ?? 'Competitive franchise in the regional league.' }}</p>
                    <div class="mt-4 grid grid-cols-3 rounded-xl bg-slate-50 p-3 text-center text-xs">
                        <div><span class="block text-slate-400">Wins</span><strong class="mt-1 block text-emerald-600">{{ $record['wins'] }}</strong></div>
                        <div><span class="block text-slate-400">Losses</span><strong class="mt-1 block text-rose-600">{{ $record['losses'] }}</strong></div>
                        <div><span class="block text-slate-400">Draws</span><strong class="mt-1 block text-slate-700">{{ $record['draws'] }}</strong></div>
                    </div>
                    <a href="{{ route('teams.show', $team->slug) }}" class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-sm font-bold text-indigo-600">
                        View roster <span>&rarr;</span>
                    </a>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center text-sm text-slate-500">No teams match your search.</div>
            @endforelse
        </div>
        <div>{{ $teams->links() }}</div>
    </div>
</x-app-layout>
