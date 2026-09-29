<x-app-layout>
    <div class="max-w-md mx-auto my-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] sm:p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 to-sky-400"></div>

            <div class="text-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                </div>
                <h1 class="text-2xl font-black text-white">Sign In to LeagueHub</h1>
                <p class="text-xs text-gray-400 mt-1">Access tournaments, schedules, rosters, and live consoles</p>
            </div>

            <!-- Demo fast-fill accounts -->
            <div class="mb-6 p-3 rounded-xl bg-gray-900/60 border border-gray-700/80">
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Quick Demo One-Click Sign In:</div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('demo.switch', 'admin') }}" class="p-2 rounded-lg bg-gray-800 hover:bg-cyan-500/20 border border-gray-700 hover:border-cyan-500/50 text-cyan-400 text-center font-medium transition">
                        🛡️ Admin
                    </a>
                    <a href="{{ route('demo.switch', 'manager') }}" class="p-2 rounded-lg bg-gray-800 hover:bg-sky-500/20 border border-gray-700 hover:border-sky-500/50 text-sky-400 text-center font-medium transition">
                        👔 Manager
                    </a>
                    <a href="{{ route('demo.switch', 'player') }}" class="p-2 rounded-lg bg-gray-800 hover:bg-emerald-500/20 border border-gray-700 hover:border-emerald-500/50 text-emerald-400 text-center font-medium transition">
                        ⚡ Player
                    </a>
                    <a href="{{ route('demo.switch', 'viewer') }}" class="p-2 rounded-lg bg-gray-800 hover:bg-purple-500/20 border border-gray-700 hover:border-purple-500/50 text-purple-400 text-center font-medium transition">
                        👁️ Viewer
                    </a>
                </div>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block mb-1.5 text-xs font-medium text-gray-300">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5 placeholder-gray-500"
                        placeholder="you@leagueplatform.com">
                </div>

                <div>
                    <label for="password" class="block mb-1.5 text-xs font-medium text-gray-300">Password</label>
                    <input type="password" id="password" name="password" required
                        class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5 placeholder-gray-500"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-4 h-4 text-cyan-600 bg-gray-900 border-gray-700 rounded focus:ring-cyan-500">
                        <label for="remember" class="ml-2 text-xs font-medium text-gray-400">Remember me</label>
                    </div>
                </div>

                <button type="submit"
                    class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 hover:to-sky-300 font-bold rounded-lg text-sm px-5 py-2.5 text-center shadow-lg shadow-cyan-500/20 transition-all">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-gray-400">
                Don't have an account yet?
                <a href="{{ route('register') }}" class="text-cyan-400 hover:underline font-semibold ml-1">Register now</a>
            </div>
        </div>
    </div>
</x-app-layout>

