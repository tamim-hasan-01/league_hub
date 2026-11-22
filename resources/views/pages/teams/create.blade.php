<x-app-layout>
    <div class="mx-auto max-w-2xl">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_16px_40px_rgba(15,23,42,0.07)] sm:p-8">
            <a href="{{ route('teams.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">&larr; Teams directory</a>
            <p class="mt-6 text-sm font-semibold text-indigo-600">New franchise</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Create a team</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Register the team profile and prepare a roster for competitions.</p>

            <form action="{{ route('teams.store') }}" method="POST" class="mt-7 space-y-5">
                @csrf
                <div>
                    <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Team name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900 placeholder:text-slate-400"
                           placeholder="Apex Sentinels FC">
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="sport_type" class="mb-2 block text-sm font-bold text-slate-700">Sport</label>
                        <select name="sport_type" id="sport_type" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900">
                            <option value="Football">Football / Soccer</option>
                            <option value="Basketball">Basketball</option>
                            <option value="Esports">Esports</option>
                            <option value="Cricket">Cricket</option>
                        </select>
                    </div>
                    <div>
                        <label for="home_venue" class="mb-2 block text-sm font-bold text-slate-700">Home venue</label>
                        <input type="text" name="home_venue" id="home_venue" value="{{ old('home_venue') }}"
                               class="h-12 w-full rounded-xl border border-slate-200 px-3 text-sm text-slate-900 placeholder:text-slate-400"
                               placeholder="Vanguard Park Arena">
                    </div>
                </div>
                <div>
                    <label for="description" class="mb-2 block text-sm font-bold text-slate-700">Description</label>
                    <textarea name="description" id="description" rows="5"
                              class="w-full rounded-xl border border-slate-200 px-3 py-3 text-sm leading-6 text-slate-900 placeholder:text-slate-400"
                              placeholder="Team identity, playing style, and competition goals">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="h-12 w-full rounded-xl bg-slate-950 text-sm font-bold text-white hover:bg-indigo-600">Register team</button>
            </form>
        </section>
    </div>
</x-app-layout>
