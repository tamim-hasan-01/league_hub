@props(['match', 'compact' => false])

@php
$isLive = $match->status === 'live';
$isUpcoming = $match->status === 'upcoming';
$isCompleted = $match->status === 'completed';
$userBookmarked = auth()->check() && auth()->user()->bookmarks()->where('game_match_id', $match->id)->exists();
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_10px_28px_rgba(15,23,42,0.07)] transition hover:-translate-y-0.5 hover:border-indigo-200']) }}>
    <!-- Top Meta Line -->
    <div class="flex min-h-12 items-start justify-between gap-3 border-b border-slate-100 pb-3">
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

            <span class="line-clamp-2 max-w-[180px] text-sm font-semibold leading-5 text-slate-600" title="{{ $match->tournament->title ?? 'Exhibition Friendly' }}">
                {{ $match->tournament->title ?? 'Exhibition Match' }}
            </span>
        </div>

        <div class="flex items-center gap-1.5 text-gray-400">
            <span class="shrink-0 rounded-md border border-slate-200 bg-slate-100 px-2 py-1 text-xs font-semibold capitalize text-slate-600">
                {{ str_replace('_', ' ', $match->stage) }}
            </span>

            @auth
            <form action="{{ route('bookmarks.toggle') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="type" value="match">
                <input type="hidden" name="id" value="{{ $match->id }}">
                <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition" title="{{ $userBookmarked ? 'Remove Bookmark' : 'Bookmark Match' }}">
                    <svg class="w-4 h-4 {{ $userBookmarked ? 'text-indigo-600 fill-indigo-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </button>
            </form>
            @endauth
        </div>
    </div>

    <!-- Match Teams and Scores -->
    <div class="grid grid-cols-[1fr_74px_1fr] items-center gap-3 py-6">
        <!-- Home Team -->
        <div class="flex min-w-0 flex-col">
            <a href="{{ route('teams.show', $match->homeTeam->slug) }}" class="text-base font-bold leading-6 text-slate-950 transition hover:text-indigo-600">
                {{ $match->homeTeam->name }}
            </a>
            <span class="mt-1 text-xs font-medium text-slate-400">Home</span>
        </div>

        <!-- Score or VS -->
        <div class="flex items-center justify-center">
            @if($isUpcoming)
                <div class="flex h-12 w-[74px] items-center justify-center rounded-xl border border-slate-200 bg-slate-100 text-sm font-bold tracking-[0.12em] text-slate-700">
                    VS
                </div>
            @else
                <div class="flex h-12 w-[74px] items-center justify-center rounded-xl border border-slate-200 bg-slate-950 px-2 tabular-nums text-white">
                    <span class="text-lg font-bold {{ $match->home_score > $match->away_score ? 'text-indigo-300' : '' }}">
                        {{ $match->home_score }}
                    </span>
                    <span class="mx-1 text-slate-400">:</span>
                    <span class="text-lg font-bold {{ $match->away_score > $match->home_score ? 'text-indigo-300' : '' }}">
                        {{ $match->away_score }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Away Team -->
        <div class="flex min-w-0 flex-col items-end text-right">
            <a href="{{ route('teams.show', $match->awayTeam->slug) }}" class="text-base font-bold leading-6 text-slate-950 transition hover:text-indigo-600">
                {{ $match->awayTeam->name }}
            </a>
            <span class="mt-1 text-xs font-medium text-slate-400">Away</span>
        </div>
    </div>

    <!-- Bottom Meta: Venue & Kickoff -->
    <div class="mt-1 flex items-center justify-between gap-3 border-t border-slate-100 pt-3 text-sm text-slate-500">
        <div class="flex items-center gap-1.5 truncate">
            <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="truncate">{{ $match->venue }}</span>
        </div>

        <div class="flex shrink-0 items-center gap-1.5 font-semibold text-indigo-600">
            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span>{{ $match->scheduled_at->format('M d, H:i') }}</span>
        </div>
    </div>
</div>

