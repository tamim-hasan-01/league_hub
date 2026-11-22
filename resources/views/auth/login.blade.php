<x-app-layout>
    <div class="mx-auto max-w-5xl">
        <div class="grid overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.08)] lg:grid-cols-[0.9fr_1.1fr]">
            <aside class="relative hidden overflow-hidden bg-slate-950 px-10 py-12 text-white lg:block">
                <div class="absolute right-0 top-0 h-full w-full bg-[radial-gradient(circle_at_80%_20%,rgba(129,140,248,0.24),transparent_55%)]"></div>
                <div class="relative flex h-full flex-col justify-between">
                    <div>
                        <a href="{{ route('home') }}" class="inline-flex rounded-xl bg-white px-3 py-2">
                            <img src="{{ asset('logo.png') }}" alt="LeagueHub" class="h-9 w-auto">
                        </a>
                        <p class="mt-12 text-xs font-bold uppercase tracking-[0.18em] text-indigo-300">Welcome back</p>
                        <h1 class="mt-3 text-4xl font-black leading-tight tracking-tight">Manage every competition from one account.</h1>
                        <p class="mt-5 max-w-md text-sm leading-7 text-slate-300">
                            Follow fixtures, manage team rosters, review tournament brackets, and stay connected to every match update.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/10">
                        <div class="bg-slate-950/80 p-4">
                            <p class="text-2xl font-black">Live</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wider text-slate-400">Match activity</p>
                        </div>
                        <div class="bg-slate-950/80 p-4">
                            <p class="text-2xl font-black">Secure</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wider text-slate-400">Role access</p>
                        </div>
                    </div>
                </div>
            </aside>

            <section class="px-5 py-8 sm:px-10 sm:py-10 lg:px-12">
                <div class="mb-7">
                    <p class="text-sm font-semibold text-indigo-600">Account access</p>
                    <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Sign in to LeagueHub</h2>
                    <p class="mt-2 text-sm text-slate-500">Enter your account credentials to continue.</p>
                </div>

                <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Quick demo access</p>
                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach([
                            'admin' => 'Admin',
                            'manager' => 'Manager',
                            'player' => 'Player',
                            'viewer' => 'Viewer',
                        ] as $role => $label)
                            <a href="{{ route('demo.switch', $role) }}"
                               class="flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-700 shadow-sm transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="h-12 w-full rounded-xl border border-slate-200 px-3.5 text-sm text-slate-900 placeholder:text-slate-400"
                               placeholder="you@leaguehub.com">
                        @error('email')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-bold text-slate-700">Password</label>
                        <input type="password" id="password" name="password" required
                               class="h-12 w-full rounded-xl border border-slate-200 px-3.5 text-sm text-slate-900 placeholder:text-slate-400"
                               placeholder="Enter your password">
                    </div>

                    <label class="flex items-center gap-2.5">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-slate-600">Keep me signed in</span>
                    </label>

                    <button type="submit"
                            class="h-12 w-full rounded-xl bg-slate-950 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-600">
                        Sign in
                    </button>
                </form>

                <p class="mt-7 text-center text-sm text-slate-500">
                    New to LeagueHub?
                    <a href="{{ route('register') }}" class="ml-1 font-bold text-indigo-600 hover:text-indigo-800">Create an account</a>
                </p>
            </section>
        </div>
    </div>
</x-app-layout>
