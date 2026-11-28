<x-app-layout>
    <div class="space-y-8" x-data="{ createOpen: @js($errors->any()) }">
        <div class="flex flex-col gap-5 border-b border-slate-200 pb-7 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Competition directory</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Tournaments</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $activeCount }} active competitions across every discipline</p>
            </div>
            @can('create-tournament')
                <button type="button" @click="createOpen = !createOpen" :aria-expanded="createOpen.toString()" aria-controls="create-tournament-form" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-indigo-600">
                    Create tournament
                </button>
            @else
                @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:border-indigo-200 hover:text-indigo-600">
                        Sign in as an administrator to create a tournament
                    </a>
                @endguest
            @endcan
        </div>

        @can('create-tournament')
            <section id="create-tournament-form" x-show="createOpen" x-cloak class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="mb-6">
                    <h2 class="text-xl font-black text-slate-950">Create a tournament</h2>
                    <p class="mt-1 text-sm text-slate-500">Set up the competition details and open team registration.</p>
                </div>
                <form action="{{ route('tournaments.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="title" class="mb-2 block text-sm font-bold text-slate-700">Tournament name</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                                   class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900 placeholder:text-slate-400"
                                   placeholder="Summer Championship">
                        </div>
                        <div>
                            <label for="sport_type" class="mb-2 block text-sm font-bold text-slate-700">Sport</label>
                            <select name="sport_type" id="sport_type" required class="h-12 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900">
                                @foreach(['Football', 'Basketball', 'Esports', 'Cricket'] as $sport)
                                    <option value="{{ $sport }}" @selected(old('sport_type', request('sport', 'Football')) === $sport)>{{ $sport }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="mb-2 block text-sm font-bold text-slate-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-3 text-sm leading-6 text-slate-900 placeholder:text-slate-400" placeholder="What should teams know about this competition?">{{ old('description') }}</textarea>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="venue" class="mb-2 block text-sm font-bold text-slate-700">Venue</label>
                            <input type="text" name="venue" id="venue" value="{{ old('venue') }}" maxlength="255" class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900" placeholder="Competition venue">
                        </div>
                        <div>
                            <label for="start_date" class="mb-2 block text-sm font-bold text-slate-700">Start date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900">
                        </div>
                        <div>
                            <label for="end_date" class="mb-2 block text-sm font-bold text-slate-700">End date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900">
                        </div>
                        <div>
                            <label for="prize_pool" class="mb-2 block text-sm font-bold text-slate-700">Prize pool</label>
                            <input type="text" name="prize_pool" id="prize_pool" value="{{ old('prize_pool') }}" maxlength="100" class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900" placeholder="Trophy">
                        </div>
                        <div>
                            <label for="max_teams" class="mb-2 block text-sm font-bold text-slate-700">Maximum teams</label>
                            <input type="number" name="max_teams" id="max_teams" value="{{ old('max_teams', 8) }}" required min="2" max="32" class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900">
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                        <button type="button" @click="createOpen = false" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-600">Create tournament</button>
                    </div>
                </form>
            </section>
        @endcan

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
