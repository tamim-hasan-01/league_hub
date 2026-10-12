<x-app-layout>
    <div class="space-y-10" x-data="{ tab: 'roster' }">
        <!-- Team Header Card -->
        <div class="relative overflow-hidden rounded-3xl bg-gray-800 border border-gray-700 p-6 sm:p-8 shadow-2xl">
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div class="flex items-start sm:items-center gap-4 sm:gap-6">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-cyan-600 to-sky-400 flex items-center justify-center font-black text-white text-2xl sm:text-3xl shadow-xl shrink-0">
                        {{ substr($team->name, 0, 2) }}
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-cyan-500/15 text-cyan-400 border border-cyan-500/30">
                                {{ $team->sport_type }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded text-xs font-mono font-bold text-sky-400 bg-gray-900 border border-gray-700">
                                Regional Rank #{{ $team->areal_ranking }}
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">{{ $team->name }}</h1>
                        <p class="text-xs text-gray-400">
                            Home Venue: <span class="text-gray-200 font-medium">{{ $team->home_venue ?? 'City Arena' }}</span>
                            &bull; Manager: <span class="text-cyan-400 font-semibold">{{ $team->manager->name ?? 'Staff' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Manager Action Buttons -->
                @can('manage-team', $team)
                    <div class="flex flex-wrap items-center gap-2.5">
                        <button data-modal-target="recruit-player-modal" data-modal-toggle="recruit-player-modal" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-gray-950 shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            <span>Recruit Player</span>
                        </button>

                        <button data-modal-target="propose-friendly-modal" data-modal-toggle="propose-friendly-modal" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Propose Friendly Match</span>
                        </button>

                        <a href="{{ route('teams.edit', $team->id) }}" class="p-2 rounded-xl bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white transition" title="Edit Team">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </a>
                    </div>
                @endcan
            </div>

            <!-- Win Loss Stats Pill Grid -->
            @php $record = $team->record; @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-gray-700/60">
                <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/60 text-center">
                    <span class="text-gray-400 text-[11px] block uppercase font-semibold">Total Fixtures</span>
                    <span class="text-xl font-black text-white">{{ $record['total'] }}</span>
                </div>
                <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/60 text-center">
                    <span class="text-gray-400 text-[11px] block uppercase font-semibold">Victories</span>
                    <span class="text-xl font-black text-emerald-400">{{ $record['wins'] }}</span>
                </div>
                <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/60 text-center">
                    <span class="text-gray-400 text-[11px] block uppercase font-semibold">Defeats</span>
                    <span class="text-xl font-black text-rose-400">{{ $record['losses'] }}</span>
                </div>
                <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/60 text-center">
                    <span class="text-gray-400 text-[11px] block uppercase font-semibold">Draws</span>
                    <span class="text-xl font-black text-gray-300">{{ $record['draws'] }}</span>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex items-center gap-3 border-b border-gray-800 pb-2">
            <button @click="tab = 'roster'" :class="tab === 'roster' ? 'text-cyan-400 border-b-2 border-cyan-400 font-bold' : 'text-gray-400 hover:text-white'" class="px-4 py-2 text-sm transition">
                Roster & Player Health ({{ $team->players->count() }})
            </button>
            <button @click="tab = 'matches'" :class="tab === 'matches' ? 'text-cyan-400 border-b-2 border-cyan-400 font-bold' : 'text-gray-400 hover:text-white'" class="px-4 py-2 text-sm transition">
                Historical Matches & Schedule ({{ $matches->count() }})
            </button>
            <button @click="tab = 'tournaments'" :class="tab === 'tournaments' ? 'text-cyan-400 border-b-2 border-cyan-400 font-bold' : 'text-gray-400 hover:text-white'" class="px-4 py-2 text-sm transition">
                Tournament Affiliations ({{ $team->tournaments->count() }})
            </button>
        </div>

        <!-- TAB 1: Active Roster & Injury Badges -->
        <div x-show="tab === 'roster'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">Active Roster & Medical Status</h2>
                    <p class="text-xs text-gray-400">Players contracted under {{ $team->name }}. Players on medical hold are marked with injury warnings.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($team->players as $player)
                    @php $profile = $player->playerProfile; @endphp
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-4 shadow-lg flex flex-col justify-between relative overflow-hidden">
                        @if($profile && $profile->is_injured)
                            <div class="absolute top-0 right-0 left-0 bg-red-600/20 border-b border-red-500/40 text-red-300 text-[10px] font-bold py-0.5 px-3 flex items-center justify-between">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    MEDICAL HOLD (INJURED)
                                </span>
                                <span>No. {{ $profile->jersey_number ?? '10' }}</span>
                            </div>
                        @endif

                        <div class="pt-3">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-900 border border-gray-700 flex items-center justify-center font-bold text-cyan-400 text-sm">
                                        #{{ $profile->jersey_number ?? rand(1, 99) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-sm">{{ $player->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $player->pivot->position ?? 'Starter' }}</div>
                                    </div>
                                </div>

                                <div>
                                    @if($profile && $profile->is_injured)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/15 text-red-400 border border-red-500/30">
                                            Injured
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                            Fit / Active
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($profile && $profile->is_injured && $profile->injury_notes)
                                <div class="mb-3 p-2 bg-red-950/30 rounded-lg border border-red-900/40 text-[11px] text-red-300">
                                    <span class="font-semibold block text-red-200">Physio Notes:</span>
                                    {{ $profile->injury_notes }}
                                </div>
                            @endif

                            @if($profile && $profile->career_stats)
                                <div class="grid grid-cols-3 gap-1.5 p-2 bg-gray-900/60 rounded-xl text-center text-xs">
                                    <div>
                                        <span class="text-gray-500 block text-[10px]">Form</span>
                                        <span class="text-cyan-400 font-bold">{{ $profile->career_stats['form_rating'] ?? 7.5 }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block text-[10px]">Matches</span>
                                        <span class="text-white font-bold">{{ $profile->career_stats['matches_played'] ?? 0 }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block text-[10px]">Points/Goals</span>
                                        <span class="text-sky-400 font-bold">{{ $profile->career_stats['goals_or_points'] ?? 0 }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @can('manage-team', $team)
                            <div class="mt-3 pt-2.5 border-t border-gray-700/60 flex items-center justify-end">
                                <form action="{{ route('teams.removePlayer', [$team->id, $player->id]) }}" method="POST" onsubmit="return confirm('Remove player from roster?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-medium">Remove from Roster</button>
                                </form>
                            </div>
                        @endcan
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 bg-gray-800/40 rounded-xl border border-gray-700 text-gray-400 text-xs">
                        No players currently enrolled in this roster.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 2: Historical Match Results -->
        <div x-show="tab === 'matches'" x-cloak class="space-y-4">
            <h2 class="text-xl font-bold text-white">Historical Fixtures & Match Logs</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($matches as $match)
                    <x-match-card :match="$match" />
                @empty
                    <div class="col-span-3 text-center py-8 bg-gray-800/40 rounded-xl border border-gray-700 text-gray-400 text-xs">
                        No fixtures recorded for this team yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 3: Tournament Affiliations -->
        <div x-show="tab === 'tournaments'" x-cloak class="space-y-4">
            <h2 class="text-xl font-bold text-white">Participating Tournaments</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($team->tournaments as $t)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <a href="{{ route('tournaments.show', $t->slug) }}" class="font-bold text-white hover:text-cyan-400 transition block">
                                {{ $t->title }}
                            </a>
                            <span class="text-xs text-gray-400">{{ $t->sport_type }} &bull; Phase: {{ $t->current_stage }}</span>
                        </div>
                        <a href="{{ route('tournaments.show', $t->slug) }}" class="text-cyan-400 text-xs font-semibold hover:underline">
                            View &rarr;
                        </a>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8 bg-gray-800/40 rounded-xl border border-gray-700 text-gray-400 text-xs">
                        Team has not yet entered any public tournament.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Recruit Player Modal (For Manager) -->
    @can('manage-team', $team)
    <div id="recruit-player-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-700">
                    <h3 class="text-base font-bold text-white">Recruit Athlete to {{ $team->name }}</h3>
                    <button type="button" class="text-gray-400 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" data-modal-hide="recruit-player-modal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>

                <form action="{{ route('teams.addPlayer', $team->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="player_id" class="block mb-1 text-xs font-medium text-gray-300">Select Available Player</label>
                        <select name="player_id" id="player_id" required class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                            @foreach($availablePlayers as $avail)
                                <option value="{{ $avail->id }}">
                                    {{ $avail->name }} (Rank #{{ $avail->playerProfile->areal_ranking ?? '50' }} &bull; {{ $avail->areal_zone ?? 'Metro' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="position" class="block mb-1 text-xs font-medium text-gray-300">Roster Position</label>
                        <select name="position" id="position" required class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                            <option value="Starting Forward / Striker">Starting Forward / Striker</option>
                            <option value="Midfield Maestro">Midfield Maestro</option>
                            <option value="Key Defender">Key Defender</option>
                            <option value="Goalkeeper / Anchor">Goalkeeper / Anchor</option>
                            <option value="Entry Fragger / Duelist">Entry Fragger / Duelist</option>
                            <option value="Tactical In-Game Leader (IGL)">Tactical In-Game Leader (IGL)</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition">
                        Confirm Roster Contract
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Propose Friendly Match Modal -->
    <div id="propose-friendly-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-700">
                    <h3 class="text-base font-bold text-white">Propose Exhibition Friendly Match</h3>
                    <button type="button" class="text-gray-400 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" data-modal-hide="propose-friendly-modal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>

                <form action="{{ route('teams.proposeFriendly', $team->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="opponent_team_id" class="block mb-1 text-xs font-medium text-gray-300">Opponent Team</label>
                        <select name="opponent_team_id" id="opponent_team_id" required class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                            @foreach($otherTeams as $opp)
                                <option value="{{ $opp->id }}">{{ $opp->name }} (Rank #{{ $opp->areal_ranking }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="scheduled_at" class="block mb-1 text-xs font-medium text-gray-300">Date & Kickoff Time</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" required class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5">
                    </div>

                    <div>
                        <label for="venue" class="block mb-1 text-xs font-medium text-gray-300">Venue / Location</label>
                        <input type="text" name="venue" id="venue" value="{{ $team->home_venue ?? 'City Sports Complex' }}" required class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg block w-full p-2.5">
                    </div>

                    <button type="submit" class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition">
                        Dispatch Callups & Propose Match
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan
</x-app-layout>

