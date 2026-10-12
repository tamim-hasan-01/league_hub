@props(['match', 'compact' => false])

@php
$isLive = $match->status === 'live';
$isUpcoming = $match->status === 'upcoming';
$isCompleted = $match->status === 'completed';
$userBookmarked = auth()->check() && auth()->user()->bookmarks()->where('game_match_id', $match->id)->exists();
@endphp

<div {{ $attributes->merge(['class' => 'bg-gray-800/90 hover:bg-gray-800 border border-gray-700/80 hover:border-cyan-500/50 rounded-xl p-4 transition-all duration-200 shadow-lg relative group overflow-hidden']) }}>
    <!-- Top Meta Line -->
    <div class="flex items-center justify-between gap-2 mb-3 pb-2.5 border-b border-gray-700/60 text-xs">
        <div class="flex items-center gap-2">
            @if($isLive)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-400 border border-rose-500/30 animate-pulse">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5 animate-ping"></span>
                    LIVE NOW
                </span>
            @elseif($isUpcoming)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-500/10 text-sky-400 border border-sky-500/30">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Upcoming
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-700/70 text-gray-300 border border-gray-600/50">
                    Final Result
                </span>
            @endif

            <span class="text-gray-400 truncate max-w-[140px] font-medium" title="{{ $match->tournament->title ?? 'Exhibition Friendly' }}">
                {{ $match->tournament->title ?? 'Exhibition Match' }}
            </span>
        </div>

        <div class="flex items-center gap-1.5 text-gray-400">
            <span class="px-2 py-0.5 rounded text-[11px] bg-gray-900/60 border border-gray-700 text-cyan-400 uppercase tracking-wider font-semibold">
                {{ str_replace('_', ' ', $match->stage) }}
            </span>

            @auth
            <form action="{{ route('bookmarks.toggle') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="type" value="match">
                <input type="hidden" name="id" value="{{ $match->id }}">
                <button type="submit" class="p-1 rounded hover:bg-gray-700 text-gray-400 hover:text-cyan-400 transition" title="{{ $userBookmarked ? 'Remove Bookmark' : 'Bookmark Match' }}">
                    <svg class="w-4 h-4 {{ $userBookmarked ? 'text-cyan-400 fill-cyan-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </button>
            </form>
            @endauth
        </div>
    </div>

    <!-- Match Teams and Scores -->
    <div class="grid grid-cols-7 items-center gap-2 py-2">
        <!-- Home Team -->
        <div class="col-span-3 flex flex-col items-center sm:items-start text-center sm:text-left">
            <a href="{{ route('teams.show', $match->homeTeam->slug) }}" class="group-hover:text-cyan-400 transition font-bold text-white text-sm sm:text-base truncate w-full">
                {{ $match->homeTeam->name }}
            </a>
            <span class="text-xs text-gray-400">Home</span>
        </div>

        <!-- Score or VS -->
        <div class="col-span-1 flex flex-col items-center justify-center">
            @if($isUpcoming)
                <div class="text-xs font-bold text-cyan-400 bg-gray-900/80 px-2.5 py-1 rounded-md border border-gray-700">
                    VS
                </div>
            @else
                <div class="flex items-center gap-1.5 px-3 py-1 bg-gray-900/90 rounded-lg border border-gray-700">
                    <span class="text-base sm:text-xl font-black {{ $match->home_score > $match->away_score ? 'text-cyan-400' : 'text-white' }}">
                        {{ $match->home_score }}
                    </span>
                    <span class="text-gray-500 font-bold">:</span>
                    <span class="text-base sm:text-xl font-black {{ $match->away_score > $match->home_score ? 'text-cyan-400' : 'text-white' }}">
                        {{ $match->away_score }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Away Team -->
        <div class="col-span-3 flex flex-col items-center sm:items-end text-center sm:text-right">
            <a href="{{ route('teams.show', $match->awayTeam->slug) }}" class="group-hover:text-cyan-400 transition font-bold text-white text-sm sm:text-base truncate w-full">
                {{ $match->awayTeam->name }}
            </a>
            <span class="text-xs text-gray-400">Away</span>
        </div>
    </div>

    <!-- Bottom Meta: Venue & Kickoff -->
    <div class="mt-3 pt-2.5 border-t border-gray-700/60 flex items-center justify-between text-xs text-gray-400">
        <div class="flex items-center gap-1.5 truncate">
            <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="truncate">{{ $match->venue }}</span>
        </div>

        <div class="flex items-center gap-1 shrink-0 text-gray-300 font-mono">
            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span>{{ $match->scheduled_at->format('M d, H:i') }}</span>
        </div>
    </div>
</div>

