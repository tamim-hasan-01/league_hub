<x-app-layout>
    <div class="space-y-8">
        <!-- Hub Top Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gray-800 border border-gray-700 p-6 sm:p-8 shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">Personalized Mission Hub</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-700 text-gray-200 border border-gray-600">
                            Role: {{ $user->role }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white">Welcome, {{ $user->name }}</h1>
                    <p class="text-xs sm:text-sm text-gray-400 mt-1">
                        @if($user->isManager())
                            Oversee your team franchises, submit tournament registrations, and manage friendly schedules.
                        @elseif($user->isPlayer())
                            Review match invitations and call-ups, respond with contract acceptance, and check squad status.
                        @elseif($user->isViewer())
                            Track bookmarked matches, monitor subscribed tournament brackets, and get live scoring alerts.
                        @else
                            Operations dashboard for multi-sport leagues, live match consoles, and health tracking.
                        @endif
                    </p>
                </div>

                <!-- Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    @if($user->isManager())
                        <a href="{{ route('teams.create') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-gray-950 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Found New Team</span>
                        </a>
                    @elseif($user->isPlayer())
                        <a href="{{ route('mystats') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-gray-950 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span>My Stats & Form</span>
                        </a>
                    @elseif($user->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-gray-950 transition flex items-center gap-1.5">
                            <span>Open Authority Console</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- 1. MANAGER VIEW -->
        @if($user->isManager() || $user->isAdmin())
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        👔 Managerial Headquarters
                    </h2>
                    <span class="text-xs text-gray-400">{{ count($managerData['myTeams'] ?? []) }} Franchises Owned</span>
                </div>

                <!-- Managed Teams Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($managerData['myTeams'] ?? [] as $team)
                        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-4 shadow-lg flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-cyan-400">{{ $team->sport_type }}</span>
                                    <span class="text-xs font-mono text-gray-400">Rank #{{ $team->areal_ranking }}</span>
                                </div>
                                <h3 class="text-base font-bold text-white mb-1">
                                    <a href="{{ route('teams.show', $team->slug) }}" class="hover:text-cyan-400 transition">{{ $team->name }}</a>
                                </h3>
                                <div class="text-xs text-gray-400 mb-3">{{ $team->players->count() }} Contracted Players</div>
                            </div>
                            <div class="flex items-center gap-2 pt-2 border-t border-gray-700/60">
                                <a href="{{ route('teams.show', $team->slug) }}" class="text-xs text-cyan-400 hover:underline font-semibold flex-1">Manage Roster &rarr;</a>
                                <a href="{{ route('teams.edit', $team->id) }}" class="text-xs text-gray-400 hover:text-white">Edit</a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 p-6 text-center text-xs text-gray-400 bg-gray-800/40 rounded-2xl border border-gray-700">
                            You have not founded any teams yet. Create one to start recruiting players!
                        </div>
                    @endforelse
                </div>

                <!-- Pending Tournament Applications -->
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5 shadow-xl space-y-4">
                    <h3 class="text-base font-bold text-white">Tournament Applications Status</h3>
                    <div class="divide-y divide-gray-700">
                        @forelse($managerData['pendingApplications'] ?? [] as $app)
                            <div class="py-3 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-white">{{ $app->team->name }}</span>
                                    <span class="text-gray-400 mx-1">&rarr;</span>
                                    <a href="{{ route('tournaments.show', $app->tournament->slug) }}" class="text-cyan-400 hover:underline">{{ $app->tournament->title }}</a>
                                    @if($app->notes)
                                        <p class="text-[11px] text-gray-400 italic mt-0.5">Note: {{ $app->notes }}</p>
                                    @endif
                                </div>
                                <div>
                                    @if($app->status === 'approved')
                                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">APPROVED</span>
                                    @elseif($app->status === 'rejected')
                                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">REJECTED</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">UNDER REVIEW</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-gray-400">
                                No tournament applications submitted. Explore active tournaments to apply!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. PLAYER VIEW -->
        @if($user->isPlayer() || $user->isAdmin())
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        ⚡ Player Contracts & Match Call-ups
                    </h2>
                    <a href="{{ route('mystats') }}" class="text-xs text-cyan-400 hover:underline">View Performance Stats &rarr;</a>
                </div>

                <!-- Pending Invitations Actions -->
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5 shadow-xl space-y-4">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>Awaiting Response</span>
                        @if(count($playerData['pendingInvitations'] ?? []) > 0)
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                        @endif
                    </h3>

                    <div class="divide-y divide-gray-700">
                        @forelse($playerData['pendingInvitations'] ?? [] as $inv)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                                <div class="space-y-1">
                                    <div class="font-bold text-sm text-white">
                                        {{ $inv->match->homeTeam->name ?? 'Home' }} vs {{ $inv->match->awayTeam->name ?? 'Away' }}
                                    </div>
                                    <div class="text-gray-400">
                                        Tournament: <span class="text-cyan-400 font-medium">{{ $inv->match->tournament->title ?? 'Exhibition Friendly' }}</span>
                                        &bull; Venue: <span class="text-gray-300">{{ $inv->match->venue }}</span>
                                    </div>
                                    <div class="text-gray-400">
                                        Kickoff: <span class="text-sky-400 font-mono">{{ $inv->match->scheduled_at->format('M d, Y - H:i') }}</span>
                                    </div>
                                    @if($inv->manager_notes)
                                        <p class="text-[11px] text-gray-300 italic bg-gray-900/60 p-1.5 rounded-lg border border-gray-700/60">
                                            Manager Note: {{ $inv->manager_notes }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Action Buttons: Accept or Decline -->
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('invitations.respond', $inv->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow transition">
                                            ✓ Accept Contract
                                        </button>
                                    </form>

                                    <form action="{{ route('invitations.respond', $inv->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="declined">
                                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600/80 hover:bg-rose-600 text-white transition">
                                            ✕ Decline
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-gray-400">
                                No pending match call-ups requiring response.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Accepted Match Call-ups -->
                <div class="space-y-3">
                    <h3 class="text-base font-bold text-white">Confirmed Upcoming Games ({{ count($playerData['acceptedMatches'] ?? []) }})</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($playerData['acceptedMatches'] ?? [] as $inv)
                            <x-match-card :match="$inv->match" />
                        @empty
                            <div class="col-span-3 p-6 text-center text-xs text-gray-400 bg-gray-800/40 rounded-2xl border border-gray-700">
                                No upcoming matches confirmed yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <!-- 3. VIEWER VIEW -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    👁️ Viewer Bookmarks & Following
                </h2>
                <span class="text-xs text-gray-400">Custom alert & match radar</span>
            </div>

            <!-- Bookmarked Matches -->
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-gray-300 uppercase tracking-wider">Bookmarked Fixtures ({{ count($viewerData['bookmarkedMatches'] ?? []) }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($viewerData['bookmarkedMatches'] ?? [] as $bm)
                        @if($bm->match)
                            <x-match-card :match="$bm->match" />
                        @endif
                    @empty
                        <div class="col-span-3 p-6 text-center text-xs text-gray-400 bg-gray-800/40 rounded-2xl border border-gray-700">
                            No matches bookmarked yet. Click the bookmark icon on any match card to track it here!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Followed Tournaments -->
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-gray-300 uppercase tracking-wider">Followed Tournaments ({{ count($viewerData['bookmarkedTournaments'] ?? []) }})</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($viewerData['bookmarkedTournaments'] ?? [] as $bm)
                        @if($bm->tournament)
                            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-4 flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold text-cyan-400">{{ $bm->tournament->sport_type }}</span>
                                    <h4 class="text-sm font-bold text-white">
                                        <a href="{{ route('tournaments.show', $bm->tournament->slug) }}" class="hover:text-cyan-400 transition">{{ $bm->tournament->title }}</a>
                                    </h4>
                                    <span class="text-xs text-gray-400">Phase: {{ $bm->tournament->current_stage }}</span>
                                </div>
                                <form action="{{ route('bookmarks.toggle') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="tournament">
                                    <input type="hidden" name="id" value="{{ $bm->tournament->id }}">
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300">Unfollow</button>
                                </form>
                            </div>
                        @endif
                    @empty
                        <div class="col-span-3 p-6 text-center text-xs text-gray-400 bg-gray-800/40 rounded-2xl border border-gray-700">
                            No tournaments followed yet. Click "Follow Tournament" on any tournament details page.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

