<x-app-layout>
    <div class="space-y-8">
        <!-- Header & Filter Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-800">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl sm:text-3xl font-black text-white">Championship Tournaments</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                        {{ $activeCount }} Active
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Multi-sport competitive brackets, stages, and registration hubs</p>
            </div>

            @can('create-tournament')
            <button data-modal-target="create-tournament-modal" data-modal-toggle="create-tournament-modal" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-gray-950 shadow-md shadow-cyan-500/20 transition flex items-center gap-1.5 self-start md:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Launch New Tournament</span>
            </button>
            @endcan
        </div>

        <!-- Sport Filters -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('tournaments.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('sport') ? 'bg-cyan-500 text-gray-950 font-bold' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700' }}">
                All Disciplines
            </a>
            @foreach($sports as $sport)
                <a href="{{ route('tournaments.index', ['sport' => $sport]) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request('sport') === $sport ? 'bg-cyan-500 text-gray-950 font-bold' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700' }}">
                    {{ $sport }}
                </a>
            @endforeach
        </div>

        <!-- Tournaments Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tournaments as $tournament)
                <div class="bg-gray-800 border border-gray-700 hover:border-cyan-500/50 rounded-2xl p-5 shadow-xl transition flex flex-col justify-between group">
                    <div>
                        <!-- Header badge row -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                                {{ $tournament->sport_type }}
                            </span>

                            @php
                            $statusClasses = [
                                'registration_open' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
                                'in_progress' => 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30',
                                'completed' => 'bg-gray-700 text-gray-300 border border-gray-600',
                                'draft' => 'bg-gray-700 text-gray-400 border border-gray-600',
                            ][$tournament->status] ?? 'bg-gray-700 text-gray-300';
                            @endphp

                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $statusClasses }} uppercase tracking-wider">
                                {{ str_replace('_', ' ', $tournament->status) }}
                            </span>
                        </div>

                        <h2 class="text-lg font-bold text-white group-hover:text-cyan-400 transition mb-2">
                            <a href="{{ route('tournaments.show', $tournament->slug) }}">
                                {{ $tournament->title }}
                            </a>
                        </h2>

                        <p class="text-xs text-gray-400 line-clamp-2 mb-4">
                            {{ $tournament->description }}
                        </p>

                        <!-- Key Tournament Meta -->
                        <div class="grid grid-cols-2 gap-2 p-3 bg-gray-900/60 rounded-xl border border-gray-700/60 text-xs mb-4">
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-semibold">Current Phase</span>
                                <span class="text-cyan-400 font-bold capitalize">
                                    {{ str_replace('_', ' ', $tournament->current_stage) }} Stage
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-semibold">Prize Pool</span>
                                <span class="text-white font-mono font-bold">{{ $tournament->prize_pool ?? 'Glory & Trophy' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-semibold">Teams</span>
                                <span class="text-gray-300 font-bold">{{ $tournament->teams_count }} / {{ $tournament->max_teams }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-semibold">Venue</span>
                                <span class="text-gray-300 truncate block">{{ $tournament->venue ?? 'Arena' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-700/60 flex items-center justify-between text-xs">
                        <span class="text-gray-400">
                            {{ $tournament->start_date ? $tournament->start_date->format('M d, Y') : 'Schedule TBA' }}
                        </span>

                        <a href="{{ route('tournaments.show', $tournament->slug) }}" class="px-3 py-1.5 rounded-lg bg-cyan-950/70 hover:bg-cyan-900 text-cyan-400 border border-cyan-800 font-semibold transition flex items-center gap-1">
                            <span>Bracket & Details</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16 bg-gray-800/50 rounded-2xl border border-gray-700 text-gray-400 text-sm">
                    No tournaments found matching current filter.
                </div>
            @endforelse
        </div>

        <div>
            {{ $tournaments->links() }}
        </div>
    </div>
</x-app-layout>

