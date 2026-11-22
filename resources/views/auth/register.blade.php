<x-app-layout>
    <div class="mx-auto max-w-5xl" x-data="{ selectedRole: '{{ old('role', 'player') }}' }">
        <div class="grid overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.08)] lg:grid-cols-[0.78fr_1.22fr]">
            <aside class="relative hidden overflow-hidden bg-slate-950 px-10 py-12 text-white lg:block">
                <div class="absolute right-0 top-0 h-full w-full bg-[radial-gradient(circle_at_80%_20%,rgba(129,140,248,0.24),transparent_55%)]"></div>
                <div class="relative">
                    <a href="{{ route('home') }}" class="inline-flex rounded-xl bg-white px-3 py-2">
                        <img src="{{ asset('logo.png') }}" alt="LeagueHub" class="h-9 w-auto">
                    </a>
                    <p class="mt-12 text-xs font-bold uppercase tracking-[0.18em] text-indigo-300">Create your account</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight tracking-tight">Choose your place in the league.</h1>
                    <p class="mt-5 text-sm leading-7 text-slate-300">
                        Managers lead teams, players receive match call-ups, and viewers follow every fixture and tournament update.
                    </p>
                    <div class="mt-10 grid gap-3">
                        @foreach([
                            ['Manager', 'Build and manage team rosters'],
                            ['Player', 'Track stats and respond to call-ups'],
                            ['Viewer', 'Follow matches and competitions'],
                        ] as [$title, $description])
                            <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                                <p class="text-sm font-bold text-white">{{ $title }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>

            <section class="px-5 py-8 sm:px-10 sm:py-10">
                <div class="mb-7">
                    <p class="text-sm font-semibold text-indigo-600">LeagueHub registration</p>
                    <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Create an account</h2>
                    <p class="mt-2 text-sm text-slate-500">Set up your profile and competition role.</p>
                </div>

                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <p class="mb-2 text-sm font-bold text-slate-700">Select your role</p>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach([
                                'manager' => 'Manager',
                                'player' => 'Player',
                                'viewer' => 'Viewer',
                            ] as $role => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="{{ $role }}" class="peer sr-only" x-model="selectedRole">
                                    <span class="flex h-20 flex-col items-center justify-center rounded-xl border border-slate-200 bg-white px-2 text-center text-sm font-bold text-slate-600 transition peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:ring-1 peer-checked:ring-indigo-500">
                                        {{ $label }}
                                        <span class="mt-1 hidden text-[11px] font-medium text-slate-400 sm:block">
                                            {{ $role === 'manager' ? 'Team control' : ($role === 'player' ? 'Match call-ups' : 'Follow updates') }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('role')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Full name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="h-12 w-full rounded-xl border border-slate-200 px-3.5 text-sm text-slate-900"
                                   placeholder="Your full name">
                        </div>
                        <div>
                            <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="h-12 w-full rounded-xl border border-slate-200 px-3.5 text-sm text-slate-900"
                                   placeholder="you@example.com">
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-2 block text-sm font-bold text-slate-700">Password</label>
                            <input type="password" id="password" name="password" required
                                   class="h-12 w-full rounded-xl border border-slate-200 px-3.5 text-sm text-slate-900"
                                   placeholder="Create a password">
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">Confirm password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="h-12 w-full rounded-xl border border-slate-200 px-3.5 text-sm text-slate-900"
                                   placeholder="Repeat password">
                        </div>
                    </div>

                    <div>
                        <label for="areal_zone" class="mb-2 block text-sm font-bold text-slate-700">Regional zone</label>
                        <select id="areal_zone" name="areal_zone" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900">
                            <option value="Metro Central" {{ old('areal_zone') === 'Metro Central' ? 'selected' : '' }}>Metro Central</option>
                            <option value="Pacific Coast" {{ old('areal_zone') === 'Pacific Coast' ? 'selected' : '' }}>Pacific Coast</option>
                            <option value="North Region" {{ old('areal_zone') === 'North Region' ? 'selected' : '' }}>North Region</option>
                            <option value="Eastern Summit" {{ old('areal_zone') === 'Eastern Summit' ? 'selected' : '' }}>Eastern Summit</option>
                            <option value="Southern Highlands" {{ old('areal_zone') === 'Southern Highlands' ? 'selected' : '' }}>Southern Highlands</option>
                        </select>
                    </div>

                    <div x-show="selectedRole === 'player'" x-cloak class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4">
                        <p class="text-sm font-bold text-indigo-800">Player profile</p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div>
                                <label for="primary_sport" class="mb-2 block text-xs font-bold text-slate-600">Primary sport</label>
                                <select id="primary_sport" name="primary_sport" class="h-11 w-full rounded-xl border border-indigo-100 bg-white px-3 text-sm text-slate-900">
                                    <option value="Football">Football / Soccer</option>
                                    <option value="Basketball">Basketball</option>
                                    <option value="Esports">Esports</option>
                                    <option value="Cricket">Cricket</option>
                                </select>
                            </div>
                            <div>
                                <label for="jersey_number" class="mb-2 block text-xs font-bold text-slate-600">Jersey number</label>
                                <input type="number" id="jersey_number" name="jersey_number" value="{{ old('jersey_number', 10) }}" min="1" max="99"
                                       class="h-11 w-full rounded-xl border border-indigo-100 bg-white px-3 text-sm text-slate-900">
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="h-12 w-full rounded-xl bg-slate-950 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-600">
                        Create account
                    </button>
                </form>

                <p class="mt-7 text-center text-sm text-slate-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="ml-1 font-bold text-indigo-600 hover:text-indigo-800">Sign in</a>
                </p>
            </section>
        </div>
    </div>
</x-app-layout>
