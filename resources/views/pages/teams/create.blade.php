<x-app-layout>
    <div class="max-w-xl mx-auto my-6">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 to-sky-400"></div>

            <div class="mb-6">
                <a href="{{ route('teams.index') }}" class="text-xs text-gray-400 hover:text-cyan-400 flex items-center gap-1 mb-2">
                    &larr; Back to Teams Directory
                </a>
                <h1 class="text-2xl font-black text-white">Establish New Team Franchise</h1>
                <p class="text-xs text-gray-400 mt-1">Found a team roster to compete in championships and friendlies</p>
            </div>

            <form action="{{ route('teams.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block mb-1 text-xs font-medium text-gray-300">Team / Franchise Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                        placeholder="Apex Sentinels FC">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sport_type" class="block mb-1 text-xs font-medium text-gray-300">Sport Discipline</label>
                        <select name="sport_type" id="sport_type" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                            <option value="Football">Football / Soccer</option>
                            <option value="Basketball">Basketball</option>
                            <option value="Esports">Esports</option>
                            <option value="Cricket">Cricket</option>
                        </select>
                    </div>

                    <div>
                        <label for="home_venue" class="block mb-1 text-xs font-medium text-gray-300">Home Arena / Stadium</label>
                        <input type="text" name="home_venue" id="home_venue" value="{{ old('home_venue') }}"
                            class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                            placeholder="Vanguard Park Arena">
                    </div>
                </div>

                <div>
                    <label for="description" class="block mb-1 text-xs font-medium text-gray-300">Description & Mission</label>
                    <textarea name="description" id="description" rows="3"
                        class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                        placeholder="Team heritage, playstyle, and championship ambitions..."></textarea>
                </div>

                <button type="submit"
                    class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 font-bold rounded-lg text-sm px-5 py-2.5 text-center shadow-lg shadow-cyan-500/20 transition-all">
                    Register Franchise
                </button>
            </form>
        </div>
    </div>
</x-app-layout>

