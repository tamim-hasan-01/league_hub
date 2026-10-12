<x-app-layout>
    <div class="max-w-xl mx-auto my-6">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 to-sky-400"></div>

            <div class="mb-6">
                <a href="{{ route('teams.show', $team->slug) }}" class="text-xs text-gray-400 hover:text-cyan-400 flex items-center gap-1 mb-2">
                    &larr; Back to {{ $team->name }}
                </a>
                <h1 class="text-2xl font-black text-white">Edit Team Details</h1>
                <p class="text-xs text-gray-400 mt-1">Update franchise information and home venue</p>
            </div>

            <form action="{{ route('teams.update', $team->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block mb-1 text-xs font-medium text-gray-300">Team Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $team->name) }}" required
                        class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sport_type" class="block mb-1 text-xs font-medium text-gray-300">Sport Discipline</label>
                        <select name="sport_type" id="sport_type" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                            <option value="Football" {{ $team->sport_type === 'Football' ? 'selected' : '' }}>Football / Soccer</option>
                            <option value="Basketball" {{ $team->sport_type === 'Basketball' ? 'selected' : '' }}>Basketball</option>
                            <option value="Esports" {{ $team->sport_type === 'Esports' ? 'selected' : '' }}>Esports</option>
                            <option value="Cricket" {{ $team->sport_type === 'Cricket' ? 'selected' : '' }}>Cricket</option>
                        </select>
                    </div>

                    <div>
                        <label for="home_venue" class="block mb-1 text-xs font-medium text-gray-300">Home Arena / Stadium</label>
                        <input type="text" name="home_venue" id="home_venue" value="{{ old('home_venue', $team->home_venue) }}"
                            class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                    </div>
                </div>

                <div>
                    <label for="description" class="block mb-1 text-xs font-medium text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">{{ old('description', $team->description) }}</textarea>
                </div>

                <button type="submit"
                    class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 font-bold rounded-lg text-sm px-5 py-2.5 text-center shadow-lg shadow-cyan-500/20 transition-all">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</x-app-layout>

