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

            <section class="px-5 py-8 sm:px-10 sm:py-10 lg:px-12" x-data="{ adminChooser: false, adminId: '', selectedRole: '', email: '{{ old('email') }}' }">
                <div class="mb-6">
                    <p class="text-sm font-semibold text-indigo-600">Account access</p>
                    <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Sign in to LeagueHub</h2>
                    <p class="mt-2 text-sm text-slate-500">Enter credentials or select a passwordless demo account below.</p>
                </div>

                <!--   Evaluation Showcase (1-Click Passwordless Demo) -->
                <div class="mb-6 rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-[0.14em] text-indigo-950">  Evaluation Access</h3>
                                <p class="text-xs text-slate-500">1-click demo account for each role &bull; No password required</p>
                            </div>
                        </div>
                        <span class="hidden sm:inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Ready
                        </span>
                    </div>

                    <div class="mt-3.5 grid grid-cols-1 gap-2.5 sm:grid-cols-3">
                        <!-- Manager Demo -->
                        <form action="{{ route('login.demo') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="manager">
                            <button type="submit"
                                    class="group flex h-full w-full flex-col justify-between rounded-xl border border-slate-200 bg-white p-3 text-left transition hover:border-amber-400 hover:bg-amber-50/30 hover:shadow-sm">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="rounded-md border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-amber-800">Manager</span>
                                        <span class="text-xs font-bold text-amber-700 transition-transform group-hover:translate-x-0.5">&rarr;</span>
                                    </div>
                                    <p class="mt-2 text-xs font-black text-slate-950"> Demo (Manager)</p>
                                    <p class="mt-0.5 text-[11px] leading-4 text-slate-500">Team headquarters, rosters, applications</p>
                                </div>
                                <span class="mt-3 inline-flex items-center text-[10px] font-bold text-emerald-600">
                                    <span class="mr-1 h-1.5 w-1.5 rounded-full bg-emerald-500"></span> 1-Click &bull; No password
                                </span>
                            </button>
                        </form>

                        <!-- Player Demo -->
                        <form action="{{ route('login.demo') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="player">
                            <button type="submit"
                                    class="group flex h-full w-full flex-col justify-between rounded-xl border border-slate-200 bg-white p-3 text-left transition hover:border-blue-400 hover:bg-blue-50/30 hover:shadow-sm">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="rounded-md border border-blue-200 bg-blue-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-blue-800">Player</span>
                                        <span class="text-xs font-bold text-blue-700 transition-transform group-hover:translate-x-0.5">&rarr;</span>
                                    </div>
                                    <p class="mt-2 text-xs font-black text-slate-950"> Demo (Player)</p>
                                    <p class="mt-0.5 text-[11px] leading-4 text-slate-500">Match call-ups, health, career analytics</p>
                                </div>
                                <span class="mt-3 inline-flex items-center text-[10px] font-bold text-emerald-600">
                                    <span class="mr-1 h-1.5 w-1.5 rounded-full bg-emerald-500"></span> 1-Click &bull; No password
                                </span>
                            </button>
                        </form>

                        <!-- Viewer Demo -->
                        <form action="{{ route('login.demo') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="viewer">
                            <button type="submit"
                                    class="group flex h-full w-full flex-col justify-between rounded-xl border border-slate-200 bg-white p-3 text-left transition hover:border-purple-400 hover:bg-purple-50/30 hover:shadow-sm">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="rounded-md border border-purple-200 bg-purple-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-purple-800">Viewer</span>
                                        <span class="text-xs font-bold text-purple-700 transition-transform group-hover:translate-x-0.5">&rarr;</span>
                                    </div>
                                    <p class="mt-2 text-xs font-black text-slate-950"> Demo (Viewer)</p>
                                    <p class="mt-0.5 text-[11px] leading-4 text-slate-500">Live fixtures, bookmarks, tournaments</p>
                                </div>
                                <span class="mt-3 inline-flex items-center text-[10px] font-bold text-emerald-600">
                                    <span class="mr-1 h-1.5 w-1.5 rounded-full bg-emerald-500"></span> 1-Click &bull; No password
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Choose an account type</p>
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
                            <button type="button" @click="selectedRole = '{{ $role }}'; adminChooser = false"
                                    class="flex h-11 items-center justify-center rounded-xl border text-sm font-bold shadow-sm transition"
                                    :class="selectedRole === '{{ $role }}' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-500' : 'border-slate-200 bg-white text-slate-700 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700'">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Role selected quick action -->
                    <div x-show="selectedRole !== '' && !adminChooser" x-cloak class="mt-3.5 rounded-xl border border-indigo-200 bg-white p-3.5 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-indigo-700" x-text="selectedRole"></span>
                                    <p class="truncate text-xs font-black text-slate-900" x-text="selectedRole === 'manager' ? '  Demo (Manager)' : (selectedRole === 'player' ? '  Demo (Player)' : '  Demo (Viewer)')"></p>
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Configured for evaluation. Instant sign in requires no password.
                                </p>
                            </div>
                            <form action="{{ route('login.demo') }}" method="POST" class="shrink-0">
                                @csrf
                                <input type="hidden" name="role" :value="selectedRole">
                                <button type="submit"
                                        class="inline-flex h-9 items-center justify-center rounded-lg bg-indigo-600 px-3.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition">
                                    ⚡ 1-Click Sign In (No Password)
                                </button>
                            </form>
                        </div>
                    </div>

                    @error('role')
                        <p class="mt-3 text-sm font-medium text-rose-600">{{ $message }}</p>
                    @enderror
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
                    <input type="hidden" name="role" :value="selectedRole">
                    <div>
                        <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" x-model="email" required autofocus
                               class="h-12 w-full rounded-xl border border-slate-200 px-3.5 text-sm text-slate-900 placeholder:text-slate-400"
                               placeholder="you@leaguehub.com">
                        @error('email')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                            <span x-show="['manager@leaguehub.com', 'player@leaguehub.com', 'viewer@leaguehub.com'].includes(email.toLowerCase().trim())" x-cloak
                                  class="text-xs font-semibold text-emerald-600">
                                Password not required for demo
                            </span>
                        </div>
                        <input type="password" id="password" name="password"
                               :required="!['manager@leaguehub.com', 'player@leaguehub.com', 'viewer@leaguehub.com'].includes(email.toLowerCase().trim())"
                               :placeholder="['manager@leaguehub.com', 'player@leaguehub.com', 'viewer@leaguehub.com'].includes(email.toLowerCase().trim()) ? 'No password required for this demo account' : 'Enter your password'"
                               class="h-12 w-full rounded-xl border border-slate-200 px-3.5 text-sm text-slate-900 placeholder:text-slate-400">
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
