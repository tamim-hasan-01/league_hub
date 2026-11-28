<x-app-layout>
    <div class="space-y-8">
        <section class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-slate-950 text-white">
            <div class="absolute right-0 top-0 h-full w-2/5 bg-[radial-gradient(circle_at_center,rgba(129,140,248,0.24),transparent_68%)]"></div>
            <div class="relative flex flex-col gap-6 px-6 py-8 sm:px-8 lg:flex-row lg:items-end lg:justify-between lg:px-10 lg:py-10">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full border border-indigo-300/20 bg-indigo-300/10 px-3 py-1 text-xs font-bold text-indigo-200">Personal hub</span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-bold capitalize text-slate-200">{{ $user->role }}</span>
                    </div>
                    <p class="mt-5 text-sm font-bold uppercase tracking-[0.16em] text-indigo-300">Competition workspace</p>
                    <h1 class="mt-2 text-3xl font-black leading-tight tracking-tight sm:text-4xl lg:text-5xl">Welcome, {{ $user->name }}</h1>
                    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                        @if($user->isManager())
                            Manage your teams, monitor tournament applications, and coordinate fixtures from one workspace.
                        @elseif($user->isPlayer())
                            Review match call-ups, respond to invitations, and keep track of confirmed games.
                        @elseif($user->isViewer())
                            Follow fixtures and tournaments, review results, and keep your competition list organized.
                        @else
                            Coordinate league operations, tournament administration, and personalized competition activity.
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 lg:justify-end">
                    @if($user->isManager())
                        <a href="{{ route('teams.create') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-slate-950 hover:bg-indigo-100">Create team</a>
                    @elseif($user->isPlayer())
                        <a href="{{ route('mystats') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-slate-950 hover:bg-indigo-100">View player stats</a>
                    @elseif($user->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-slate-950 hover:bg-indigo-100">Authority console</a>
                    @endif
                    <a href="{{ route('tournaments.index') }}" class="inline-flex h-12 items-center justify-center rounded-xl border border-white/15 bg-white/5 px-5 text-sm font-bold text-white hover:bg-white/10">Browse tournaments</a>
                </div>
            </div>
        </section>

        @if($user->isManager() || $user->isAdmin())
            <section class="space-y-5">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Manager overview</p>
                        <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Team headquarters</h2>
                    </div>
                    <p class="text-sm font-semibold text-slate-400">{{ count($managerData['myTeams'] ?? []) }} teams</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($managerData['myTeams'] ?? [] as $team)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">{{ $team->sport_type }}</p>
                                    <h3 class="mt-2 truncate text-lg font-black text-slate-950">
                                        <a href="{{ route('teams.show', $team->slug) }}" class="hover:text-indigo-600">{{ $team->name }}</a>
                                    </h3>
                                </div>
                                <span class="shrink-0 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-600">#{{ $team->areal_ranking }}</span>
                            </div>
                            <div class="mt-5 grid grid-cols-2 gap-px overflow-hidden rounded-xl border border-slate-200 bg-slate-200">
                                <div class="bg-slate-50 px-3 py-3">
                                    <p class="text-[10px] font-bold uppercase text-slate-400">Players</p>
                                    <p class="mt-1 text-xl font-black text-slate-950">{{ $team->players->count() }}</p>
                                </div>
                                <div class="bg-slate-50 px-3 py-3">
                                    <p class="text-[10px] font-bold uppercase text-slate-400">Status</p>
                                    <p class="mt-1 text-sm font-bold text-emerald-700">Active</p>
                                </div>
                            </div>
                            <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-sm">
                                <a href="{{ route('teams.show', $team->slug) }}" class="font-bold text-indigo-600 hover:text-indigo-800">Manage team &rarr;</a>
                                <a href="{{ route('teams.edit', $team->id) }}" class="font-bold text-slate-500 hover:text-slate-900">Edit</a>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                            <p class="text-base font-bold text-slate-700">No teams founded yet</p>
                            <p class="mt-1 text-sm text-slate-500">Create a team to start recruiting players.</p>
                        </div>
                    @endforelse
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Tournament invitations</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Invitations to respond to</h3>
                        </div>
                        <div class="divide-y divide-slate-100 px-5">
                            @forelse($receivedTournamentInvitations as $invitation)
                                <div class="space-y-3 py-4">
                                    <div>
                                        <a href="{{ route('tournaments.show', $invitation->tournament->slug) }}" class="font-bold text-indigo-600 hover:text-indigo-800">
                                            {{ $invitation->tournament->title }}
                                        </a>
                                        <p class="mt-1 text-sm text-slate-600">
                                            @if($invitation->target_type === 'team')
                                                Your team, {{ $invitation->team->name ?? 'team' }}, has been invited.
                                            @else
                                                You are invited to select a {{ $invitation->tournament->sport_type }} team.
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex flex-wrap items-end gap-2">
                                        @if($invitation->target_type === 'manager')
                                            @php
                                                $eligibleInvitationTeams = collect($managerData['myTeams'] ?? [])
                                                    ->where('sport_type', $invitation->tournament->sport_type);
                                            @endphp
                                            @if($eligibleInvitationTeams->isNotEmpty())
                                                <form action="{{ route('tournament-invitations.respond', $invitation) }}" method="POST" class="flex flex-wrap items-end gap-2">
                                                    @csrf
                                                    <input type="hidden" name="status" value="accepted">
                                                    <div>
                                                        <label class="mb-1 block text-xs font-bold text-slate-600">Choose a team</label>
                                                        <select name="team_id" required class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-900">
                                                            @foreach($eligibleInvitationTeams as $team)
                                                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="h-10 rounded-lg bg-emerald-600 px-4 text-sm font-bold text-white hover:bg-emerald-700">Accept invite</button>
                                                </form>
                                            @else
                                                <p class="text-xs text-amber-700">You do not currently have an eligible team for this invitation.</p>
                                            @endif
                                        @else
                                            <form action="{{ route('tournament-invitations.respond', $invitation) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="accepted">
                                                <button type="submit" class="h-10 rounded-lg bg-emerald-600 px-4 text-sm font-bold text-white hover:bg-emerald-700">Accept invite</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('tournament-invitations.respond', $invitation) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="declined">
                                            <button type="submit" class="h-10 rounded-lg border border-rose-200 bg-rose-50 px-4 text-sm font-bold text-rose-700 hover:bg-rose-100">Decline</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="py-8 text-center text-sm text-slate-500">No tournament invitations need a response.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Team membership</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Player requests</h3>
                        </div>
                        <div class="divide-y divide-slate-100 px-5">
                            @forelse($receivedTeamJoinRequests as $joinRequest)
                                <div class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-bold text-slate-950">{{ $joinRequest->player->name }}</p>
                                        <p class="mt-1 text-sm text-slate-600">
                                            Requests {{ $joinRequest->team->name }} as {{ $joinRequest->position }}
                                        </p>
                                        @if($joinRequest->notes)
                                            <p class="mt-1 text-xs text-slate-500">{{ $joinRequest->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('team-join-requests.respond', $joinRequest) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="h-9 rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700">Approve</button>
                                        </form>
                                        <form action="{{ route('team-join-requests.respond', $joinRequest) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="h-9 rounded-lg border border-rose-200 bg-rose-50 px-3 text-xs font-bold text-rose-700 hover:bg-rose-100">Decline</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="py-8 text-center text-sm text-slate-500">No player requests are waiting for review.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Tournament registrations</p>
                        <h3 class="mt-1 text-lg font-black text-slate-950">Application status</h3>
                    </div>
                    <div class="divide-y divide-slate-100 px-5">
                        @forelse($managerData['pendingApplications'] ?? [] as $application)
                            <div class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-950">{{ $application->team->name }}</p>
                                    <a href="{{ route('tournaments.show', $application->tournament->slug) }}" class="mt-1 block truncate text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                        {{ $application->tournament->title }}
                                    </a>
                                    @if($application->notes)
                                        <p class="mt-1 text-xs text-slate-400">{{ $application->notes }}</p>
                                    @endif
                                </div>
                                @if($application->status === 'approved')
                                    <span class="shrink-0 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Approved</span>
                                @elseif($application->status === 'rejected')
                                    <span class="shrink-0 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">Rejected</span>
                                @else
                                    <span class="shrink-0 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">Under review</span>
                                @endif
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-slate-500">No tournament applications submitted.</p>
                        @endforelse
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Member controls</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Player and manager directory</h3>
                        </div>
                        <span class="text-sm font-bold text-slate-400">{{ $memberDirectory->count() }} users</span>
                    </div>
                    <div class="grid max-h-[620px] gap-3 overflow-y-auto p-5 md:grid-cols-2">
                        @forelse($memberDirectory as $member)
                            @php
                                $canManageMember = $user->isAdmin()
                                    || ($user->isManager() && in_array($member->role, ['player', 'viewer'], true));
                            @endphp
                            <div class="rounded-xl border {{ $member->account_status === 'suspended' ? 'border-rose-200 bg-rose-50/50' : 'border-slate-200 bg-slate-50/60' }} p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-black text-slate-950">{{ $member->name }}</p>
                                        <p class="mt-1 truncate text-xs text-slate-500">{{ $member->email }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold uppercase text-slate-600">
                                        {{ $member->role }}
                                    </span>
                                </div>

                                @if($member->account_status === 'suspended')
                                    <div class="mt-3 rounded-lg border border-rose-100 bg-white px-3 py-2">
                                        <p class="text-xs font-bold text-rose-700">{{ $member->suspension_matches_remaining }} match suspension remaining</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ $member->suspension_reason ?: 'No reason provided' }}</p>
                                    </div>
                                    @if($canManageMember)
                                        <form action="{{ route('members.restore', $member->id) }}" method="POST" class="mt-3">
                                            @csrf
                                            <button type="submit" class="h-9 w-full rounded-lg border border-emerald-200 bg-white text-xs font-bold text-emerald-700 hover:bg-emerald-50">
                                                Restore account
                                            </button>
                                        </form>
                                    @endif
                                @elseif($canManageMember)
                                    <form action="{{ route('members.suspend', $member->id) }}" method="POST" class="mt-3 space-y-2">
                                        @csrf
                                        <div class="grid grid-cols-[1fr_1.5fr] gap-2">
                                            <select name="match_count" required class="h-9 rounded-lg border border-slate-200 bg-white px-2 text-xs font-bold text-slate-700">
                                                <option value="1">1 match</option>
                                                <option value="2">2 matches</option>
                                                <option value="3">3 matches</option>
                                            </select>
                                            <input type="text" name="reason" required maxlength="500" placeholder="Reason"
                                                   class="h-9 rounded-lg border border-slate-200 bg-white px-2.5 text-xs text-slate-900 placeholder:text-slate-400">
                                        </div>
                                        <button type="submit" class="h-9 w-full rounded-lg bg-rose-600 text-xs font-bold text-white hover:bg-rose-700">
                                            Suspend account
                                        </button>
                                    </form>
                                @else
                                    <p class="mt-3 text-xs font-bold text-slate-400">Account active</p>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full py-10 text-center text-sm text-slate-500">No members found.</div>
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        @if($user->isPlayer() || $user->isAdmin())
            <section class="space-y-5">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">Player workspace</p>
                        <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Match call-ups</h2>
                    </div>
                    <a href="{{ route('mystats') }}" class="shrink-0 text-sm font-bold text-indigo-600 hover:text-indigo-800">View stats &rarr;</a>
                </div>

                @if($user->isPlayer())
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Team membership</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Your team requests</h3>
                        </div>
                        <div class="divide-y divide-slate-100 px-5">
                            @forelse($myTeamJoinRequests as $joinRequest)
                                <div class="flex items-center justify-between gap-3 py-4">
                                    <div>
                                        <a href="{{ route('teams.show', $joinRequest->team->slug) }}" class="font-bold text-indigo-600 hover:text-indigo-800">
                                            {{ $joinRequest->team->name }}
                                        </a>
                                        <p class="mt-1 text-xs text-slate-500">Requested position: {{ $joinRequest->position }}</p>
                                    </div>
                                    <span class="rounded-full border px-3 py-1 text-xs font-bold capitalize {{ $joinRequest->status === 'approved' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($joinRequest->status === 'rejected' ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-amber-200 bg-amber-50 text-amber-700') }}">
                                        {{ $joinRequest->status }}
                                    </span>
                                </div>
                            @empty
                                <p class="py-8 text-center text-sm text-slate-500">You have not requested to join a team yet.</p>
                            @endforelse
                        </div>
                    </div>
                @endif

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Awaiting response</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Pending invitations</h3>
                        </div>
                        <span class="flex h-8 min-w-8 items-center justify-center rounded-full bg-indigo-50 px-2 text-sm font-black text-indigo-700">
                            {{ count($playerData['pendingInvitations'] ?? []) }}
                        </span>
                    </div>
                    <div class="divide-y divide-slate-100 px-5">
                        @forelse($playerData['pendingInvitations'] ?? [] as $invitation)
                            <div class="flex flex-col gap-4 py-5 lg:flex-row lg:items-center lg:justify-between">
                                <div class="min-w-0">
                                    <p class="text-base font-black text-slate-950">
                                        {{ $invitation->match->homeTeam->name ?? 'Home' }}
                                        <span class="mx-2 text-slate-400">vs</span>
                                        {{ $invitation->match->awayTeam->name ?? 'Away' }}
                                    </p>
                                    <p class="mt-2 text-sm text-slate-500">
                                        {{ $invitation->match->tournament->title ?? 'Exhibition friendly' }}
                                        <span class="mx-1 text-slate-300">&bull;</span>
                                        {{ $invitation->match->venue }}
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-indigo-600">{{ $invitation->match->scheduled_at->format('M d, Y · H:i') }}</p>
                                    @if($invitation->manager_notes)
                                        <p class="mt-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-500">{{ $invitation->manager_notes }}</p>
                                    @endif
                                </div>
                                <div class="flex shrink-0 gap-2">
                                    <form action="{{ route('invitations.respond', $invitation->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="h-11 rounded-xl bg-emerald-600 px-4 text-sm font-bold text-white hover:bg-emerald-700">Accept</button>
                                    </form>
                                    <form action="{{ route('invitations.respond', $invitation->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="declined">
                                        <button type="submit" class="h-11 rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-bold text-rose-700 hover:bg-rose-100">Decline</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="py-10 text-center text-sm text-slate-500">No pending match call-ups.</p>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Confirmed schedule</p>
                            <h3 class="mt-1 text-lg font-black text-slate-950">Upcoming games</h3>
                        </div>
                        <span class="text-sm font-bold text-slate-400">{{ count($playerData['acceptedMatches'] ?? []) }}</span>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @forelse($playerData['acceptedMatches'] ?? [] as $invitation)
                            <x-match-card :match="$invitation->match" />
                        @empty
                            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                                <p class="text-sm font-bold text-slate-600">No confirmed games yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        <section class="space-y-5">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Saved competition</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Bookmarks and following</h2>
            </div>

            <div class="space-y-4">
                <div class="flex items-end justify-between">
                    <h3 class="text-lg font-black text-slate-950">Bookmarked fixtures</h3>
                    <span class="text-sm font-bold text-slate-400">{{ count($viewerData['bookmarkedMatches'] ?? []) }}</span>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($viewerData['bookmarkedMatches'] ?? [] as $bookmark)
                        @if($bookmark->match)
                            <x-match-card :match="$bookmark->match" />
                        @endif
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                            <p class="text-sm font-bold text-slate-600">No bookmarked fixtures yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-end justify-between">
                    <h3 class="text-lg font-black text-slate-950">Followed tournaments</h3>
                    <span class="text-sm font-bold text-slate-400">{{ count($viewerData['bookmarkedTournaments'] ?? []) }}</span>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($viewerData['bookmarkedTournaments'] ?? [] as $bookmark)
                        @if($bookmark->tournament)
                            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                                <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">{{ $bookmark->tournament->sport_type }}</p>
                                <h4 class="mt-2 text-lg font-black text-slate-950">
                                    <a href="{{ route('tournaments.show', $bookmark->tournament->slug) }}" class="hover:text-indigo-600">{{ $bookmark->tournament->title }}</a>
                                </h4>
                                <p class="mt-1 text-sm capitalize text-slate-500">{{ str_replace('_', ' ', $bookmark->tournament->current_stage) }} stage</p>
                                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                                    <a href="{{ route('tournaments.show', $bookmark->tournament->slug) }}" class="text-sm font-bold text-indigo-600">View &rarr;</a>
                                    <form action="{{ route('bookmarks.toggle') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="tournament">
                                        <input type="hidden" name="id" value="{{ $bookmark->tournament->id }}">
                                        <button type="submit" class="text-sm font-bold text-rose-600 hover:text-rose-800">Unfollow</button>
                                    </form>
                                </div>
                            </article>
                        @endif
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                            <p class="text-sm font-bold text-slate-600">No tournaments followed yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
