<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'LeagueHub') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="sticky top-0 z-40 border-b border-slate-200/90 bg-white/95 backdrop-blur">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-label="Primary navigation">
            <div class="flex h-18 items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-950 text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block text-[15px] font-black tracking-[0.16em] text-slate-950">LEAGUE<span class="text-indigo-600">HUB</span></span>
                        <span class="block text-[9px] font-bold uppercase tracking-[0.24em] text-slate-400">Competition platform</span>
                    </span>
                </a>

                <div class="hidden items-center gap-1 lg:flex">
                    @php
                        $desktopLinks = [
                            ['home', 'Home', 'home'],
                            ['tournaments.index', 'Tournaments', 'tournaments.*'],
                            ['teams.index', 'Teams', 'teams.*'],
                            ['news.index', 'News', 'news.*'],
                            ['about', 'About', 'about'],
                        ];
                    @endphp
                    @foreach($desktopLinks as [$routeName, $label, $pattern])
                        <a href="{{ route($routeName) }}"
                           class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs($pattern) ? 'bg-slate-100 text-slate-950' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-950' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                    @auth
                        <a href="{{ route('mytournaments') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('mytournaments') ? 'bg-slate-100 text-slate-950' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-950' }}">My Hub</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">Console</a>
                        @endif
                    @endauth
                </div>

                <div class="flex items-center gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 sm:block">Sign in</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-600">Create account</a>
                    @endguest
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="flex h-10 items-center gap-2 rounded-full border border-slate-200 bg-white py-1 pl-1 pr-2" aria-label="Open account menu">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-950 text-xs font-bold uppercase text-white">{{ substr(auth()->user()->name, 0, 2) }}</span>
                                <span class="hidden max-w-28 truncate text-sm font-semibold text-slate-700 md:block">{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-60 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                                <div class="px-3 py-2 text-xs">
                                    <p class="font-bold text-slate-900">{{ auth()->user()->name }}</p>
                                    <p class="mt-0.5 truncate text-slate-500">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="my-1 border-t border-slate-100"></div>
                                <a href="{{ route('mytournaments') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-950">Personal hub</a>
                                @if(auth()->user()->isPlayer() || auth()->user()->isAdmin())
                                    <a href="{{ route('mystats') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-950">Player analytics</a>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50">Authority console</a>
                                @endif
                                <form action="{{ route('logout') }}" method="POST" class="mt-1 border-t border-slate-100 pt-1">
                                    @csrf
                                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-rose-600 hover:bg-rose-50">Sign out</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                    <button data-collapse-toggle="mobile-menu" type="button" class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 lg:hidden" aria-label="Toggle navigation">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
            <div id="mobile-menu" class="hidden border-t border-slate-100 pb-4 pt-2 lg:hidden">
                <div class="grid gap-1">
                    <a href="{{ route('home') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">Home</a>
                    <a href="{{ route('tournaments.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">Tournaments</a>
                    <a href="{{ route('teams.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">Teams</a>
                    <a href="{{ route('news.index') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">News</a>
                    <a href="{{ route('about') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">About</a>
                </div>
            </div>
        </nav>
    </header>

    @if(session('success') || session('error') || session('info') || $errors->any())
        <div class="mx-auto mt-5 max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-medium text-indigo-800">{{ session('info') }}</div>
            @endif
            @if($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-bold">Please correct the following:</p>
                    <ul class="mt-1 list-inside list-disc text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <main class="mx-auto min-h-[70vh] w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-8 text-xs text-slate-500 sm:px-6 md:flex-row lg:px-8">
            <p><span class="font-black tracking-widest text-slate-900">LEAGUEHUB</span> &copy; {{ date('Y') }}</p>
            <div class="flex flex-wrap justify-center gap-5">
                <a href="{{ route('tournaments.index') }}" class="hover:text-indigo-600">Tournaments</a>
                <a href="{{ route('teams.index') }}" class="hover:text-indigo-600">Teams</a>
                <a href="{{ route('news.index') }}" class="hover:text-indigo-600">News</a>
                <a href="{{ route('about') }}" class="hover:text-indigo-600">About</a>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
