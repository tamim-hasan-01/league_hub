<x-app-layout>
    <div class="max-w-lg mx-auto my-8" x-data="{ selectedRole: 'player' }">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 to-sky-400"></div>

            <div class="text-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <h1 class="text-2xl font-black text-white">Join LeagueHub</h1>
                <p class="text-xs text-gray-400 mt-1">Select your role in the ecosystem to get tailored features</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Role Selection Selector -->
                <div>
                    <label class="block mb-2 text-xs font-semibold text-gray-300 uppercase tracking-wider">Choose Your League Role</label>
                    <div class="grid grid-cols-3 gap-2">
                        <!-- Manager -->
                        <label class="cursor-pointer border rounded-xl p-3 text-center transition flex flex-col items-center gap-1.5"
                            :class="selectedRole === 'manager' ? 'border-cyan-500 bg-cyan-500/10 ring-1 ring-cyan-500 text-white' : 'border-gray-700 bg-gray-900/50 hover:bg-gray-900 text-gray-400'">
                            <input type="radio" name="role" value="manager" class="sr-only" x-model="selectedRole">
                            <span class="text-xl">👔</span>
                            <span class="text-xs font-bold">Manager</span>
                            <span class="text-[10px] text-gray-400 hidden sm:inline">Rosters & Contracts</span>
                        </label>

                        <!-- Player -->
                        <label class="cursor-pointer border rounded-xl p-3 text-center transition flex flex-col items-center gap-1.5"
                            :class="selectedRole === 'player' ? 'border-cyan-500 bg-cyan-500/10 ring-1 ring-cyan-500 text-white' : 'border-gray-700 bg-gray-900/50 hover:bg-gray-900 text-gray-400'">
                            <input type="radio" name="role" value="player" class="sr-only" x-model="selectedRole">
                            <span class="text-xl">⚡</span>
                            <span class="text-xs font-bold">Player</span>
                            <span class="text-[10px] text-gray-400 hidden sm:inline">Stats & Invites</span>
                        </label>

                        <!-- Viewer -->
                        <label class="cursor-pointer border rounded-xl p-3 text-center transition flex flex-col items-center gap-1.5"
                            :class="selectedRole === 'viewer' ? 'border-cyan-500 bg-cyan-500/10 ring-1 ring-cyan-500 text-white' : 'border-gray-700 bg-gray-900/50 hover:bg-gray-900 text-gray-400'">
                            <input type="radio" name="role" value="viewer" class="sr-only" x-model="selectedRole">
                            <span class="text-xl">👁️</span>
                            <span class="text-xs font-bold">Viewer</span>
                            <span class="text-[10px] text-gray-400 hidden sm:inline">Follow & Alerts</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block mb-1.5 text-xs font-medium text-gray-300">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                            placeholder="Alex Morgan">
                    </div>

                    <div>
                        <label for="email" class="block mb-1.5 text-xs font-medium text-gray-300">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                            placeholder="alex@team.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block mb-1.5 text-xs font-medium text-gray-300">Password</label>
                        <input type="password" id="password" name="password" required
                            class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                            placeholder="••••••••">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block mb-1.5 text-xs font-medium text-gray-300">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                            placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <label for="areal_zone" class="block mb-1.5 text-xs font-medium text-gray-300">Regional / Areal Zone</label>
                    <select id="areal_zone" name="areal_zone" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                        <option value="Metro Central">Metro Central (Division 1)</option>
                        <option value="Pacific Coast">Pacific Coast Conference</option>
                        <option value="North Region">North Regional Circuit</option>
                        <option value="Eastern Summit">Eastern Summit League</option>
                        <option value="Southern Highlands">Southern Highlands Tour</option>
                    </select>
                </div>

                <!-- Player Specific Dynamic Fields -->
                <div x-show="selectedRole === 'player'" class="p-3.5 rounded-xl bg-gray-900/60 border border-cyan-500/20 space-y-3">
                    <div class="text-xs font-bold text-cyan-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Player Profile Details
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="primary_sport" class="block mb-1 text-xs text-gray-400">Primary Sport</label>
                            <select id="primary_sport" name="primary_sport" class="bg-gray-800 border border-gray-700 text-white text-xs rounded-lg block w-full p-2">
                                <option value="Football">Football / Soccer</option>
                                <option value="Basketball">Basketball</option>
                                <option value="Esports">Esports (FPS/MOBA)</option>
                                <option value="Cricket">Cricket</option>
                            </select>
                        </div>
                        <div>
                            <label for="jersey_number" class="block mb-1 text-xs text-gray-400">Jersey Number</label>
                            <input type="number" id="jersey_number" name="jersey_number" value="10" min="1" max="99" class="bg-gray-800 border border-gray-700 text-white text-xs rounded-lg block w-full p-2">
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 hover:to-sky-300 font-bold rounded-lg text-sm px-5 py-2.5 text-center shadow-lg shadow-cyan-500/20 transition-all mt-4">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-gray-400">
                Already registered?
                <a href="{{ route('login') }}" class="text-cyan-400 hover:underline font-semibold ml-1">Sign in here</a>
            </div>
        </div>
    </div>
</x-app-layout>

