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

            <section class="px-5 py-8 sm:px-10 sm:py-10 lg:px-12" x-data="{ adminChooser: false, adminId: '' }">
                <div class="mb-7">
                    <p class="text-sm font-semibold text-indigo-600">Account access</p>
                    <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Sign in to LeagueHub</h2>
                    <p class="mt-2 text-sm text-slate-500">Enter your account credentials to continue.</p>
                </div>

                <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Quick demo access</p>
                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <button type="button" @click="adminChooser = !adminChooser; adminId = ''"
                                class="flex h-11 items-center justify-center rounded-xl border text-sm font-bold shadow-sm transition"
                                :class="adminChooser ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-500' : 'border-slate-200 bg-white text-slate-700 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700'">
                            Admin
                        </button>
                        @foreach([
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

                <div x-show="adminChooser" x-cloak
                     class="mb-6 rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-indigo-600">Administrator verification</p>
                            <p class="mt-1 text-sm font-semibold text-slate-700">Select an administrator, then enter their password.</p>
                        </div>
                        <button type="button" @click="adminChooser = false; adminId = ''"
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-indigo-100 bg-white text-slate-500 hover:text-slate-900"
                                aria-label="Close administrator verification">&times;</button>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-2">
                        @foreach([
                            'sami' => ['name' => 'Waliur Rafiq Sami', 'image' => 'dev/sami.png'],
                            'siam' => ['name' => 'Md. Tamim Hasan Siam', 'image' => 'dev/siam.jpg'],
                            'mobahir' => ['name' => 'Muhammad Mobasher Milon', 'image' => 'dev/mobahir.jpg'],
                        ] as $adminId => $admin)
                            <button type="button" @click="adminId = '{{ $adminId }}'"
                                    class="overflow-hidden rounded-xl border bg-white text-center transition"
                                    :class="adminId === '{{ $adminId }}' ? 'border-indigo-500 ring-2 ring-indigo-500/20' : 'border-slate-200 hover:border-indigo-300'">
                                <span class="block aspect-[4/5] w-full overflow-hidden bg-slate-100">
                                    <img src="{{ asset($admin['image']) }}" alt="{{ $admin['name'] }}"
                                         class="h-full w-full object-cover object-[center_18%]">
                                </span>
                                <span class="block px-2 py-2 text-[11px] font-bold leading-4 text-slate-800">
                                    {{ $admin['name'] }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <form action="{{ route('login.admin') }}" method="POST" class="mt-4 space-y-3">
                        @csrf
                        <input type="hidden" name="admin_id" :value="adminId">
                        <div>
                            <label for="admin-password" class="mb-2 block text-sm font-bold text-slate-700">
                                <span x-show="adminId === ''">Administrator password</span>
                                <span x-show="adminId !== ''">Password for
                                    <span x-text="adminId === 'sami' ? 'Waliur Rafiq Sami' : (adminId === 'siam' ? 'Md. Tamim Hasan Siam' : 'Muhammad Mobasher Milon')"></span>
                                </span>
                            </label>
                            <input type="password" id="admin-password" name="password" required
                                   :disabled="adminId === ''"
                                   class="h-12 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 disabled:cursor-not-allowed disabled:bg-slate-100"
                                   placeholder="Enter administrator password">
                            @error('password')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" :disabled="adminId === ''"
                                class="h-12 w-full rounded-xl bg-slate-950 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-600 disabled:cursor-not-allowed disabled:bg-slate-300">
                            Verify and sign in
                        </button>
                    </form>
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
                        @error('password')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
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
