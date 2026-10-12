<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-800">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-white">Registered Teams Directory</h1>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Official league rosters, managerial leadership, and competitive regional rankings</p>
            </div>

            @auth
                @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                    <a href="{{ route('teams.create') }}" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-gray-950 shadow-md shadow-cyan-500/20 transition flex items-center gap-1.5 self-start md:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Found New Team</span>
                    </a>
                @endif
            @endauth
        </div>

        <!-- Search & Filter Bar -->
        <form action="{{ route('teams.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by team name or home arena..."
                    class="bg-gray-800 border border-gray-700 text-white text-sm rounded-xl focus:ring-cyan-500 focus:border-cyan-500 block w-full pl-10 p-2.5">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <select name="sport" onchange="this.form.submit()" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-xl focus:ring-cyan-500 focus:border-cyan-500 p-2.5">
                <option value="">All Sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport }}" {{ request('sport') === $sport ? 'selected' : '' }}>{{ $sport }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-cyan-400 font-semibold text-xs rounded-xl border border-gray-700 transition">
                Search
            </button>
        </form>

        <!-- Teams Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($teams as $team)
                <div class="bg-gray-800 border border-gray-700 hover:border-cyan-500/50 rounded-2xl p-5 shadow-xl transition flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                                {{ $team->sport_type }}
                            </span>
                            <span class="text-xs font-mono font-bold text-sky-400 bg-gray-900/80 px-2 py-0.5 rounded border border-gray-700">
                                Rank #{{ $team->areal_ranking }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-cyan-600 to-sky-400 flex items-center justify-center font-black text-white text-lg shadow-md shrink-0">
                                {{ substr($team->name, 0, 2) }}
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-lg font-bold text-white group-hover:text-cyan-400 transition truncate">
                                    <a href="{{ route('teams.show', $team->slug) }}">
                                        {{ $team->name }}
                                    </a>
                                </h2>
                                <p class="text-xs text-gray-400 truncate">Manager: {{ $team->manager->name ?? 'Staff' }}</p>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 line-clamp-2 mb-4">
                            {{ $team->description ?? 'Competitive franchise contending in regional league circuits.' }}
                        </p>

                        <!-- Win Loss Stats -->
                        @php $rec = $team->record; @endphp
                        <div class="grid grid-cols-3 gap-2 p-2.5 bg-gray-900/60 rounded-xl border border-gray-700/60 text-center text-xs mb-3">
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-semibold">Wins</span>
                                <span class="text-emerald-400 font-bold font-mono">{{ $rec['wins'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-semibold">Losses</span>
                                <span class="text-rose-400 font-bold font-mono">{{ $rec['losses'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-semibold">Draws</span>
                                <span class="text-gray-300 font-bold font-mono">{{ $rec['draws'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-700/60 flex items-center justify-between text-xs">
                        <span class="text-gray-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>{{ $team->players->count() }} Roster Size</span>
                        </span>

                        <a href="{{ route('teams.show', $team->slug) }}" class="text-cyan-400 font-semibold hover:underline flex items-center gap-1">
                            <span>View Roster</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16 bg-gray-800/50 rounded-2xl border border-gray-700 text-gray-400 text-sm">
                    No teams found matching your query.
                </div>
            @endforelse
        </div>

        <div>
            {{ $teams->links() }}
        </div>
    </div>
</x-app-layout>

