<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'LeaguePlatform') }} | Esports & Sports Management Hub</title>

    <!-- Theme Initialization Script to Prevent FOUC -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage))) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 font-sans antialiased min-h-screen flex flex-col selection:bg-cyan-500 selection:text-white">

    <!-- Top Demo Switcher Bar -->
    <div class="bg-gray-950/80 border-b border-gray-800 text-xs py-1.5 px-4">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2 text-gray-400">
                <span class="inline-block w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                <span class="font-medium text-gray-300">Fast Demo Switcher:</span>
                <span class="hidden sm:inline text-gray-500">Test different user RBAC experiences instantly:</span>
            </div>
            <div class="flex items-center gap-1.5">
                <a href="{{ route('demo.switch', 'admin') }}" class="px-2 py-0.5 rounded text-[11px] font-semibold transition {{ auth()->check() && auth()->user()->isAdmin() ? 'bg-cyan-500 text-gray-950 ring-1 ring-cyan-400' : 'bg-gray-800 text-cyan-400 hover:bg-gray-700' }}">
                    League Admin
                </a>
                <a href="{{ route('demo.switch', 'manager') }}" class="px-2 py-0.5 rounded text-[11px] font-semibold transition {{ auth()->check() && auth()->user()->isManager() ? 'bg-sky-500 text-gray-950 ring-1 ring-sky-400' : 'bg-gray-800 text-sky-400 hover:bg-gray-700' }}">
                    Team Manager
                </a>
                <a href="{{ route('demo.switch', 'player') }}" class="px-2 py-0.5 rounded text-[11px] font-semibold transition {{ auth()->check() && auth()->user()->isPlayer() ? 'bg-emerald-500 text-gray-950 ring-1 ring-emerald-400' : 'bg-gray-800 text-emerald-400 hover:bg-gray-700' }}">
                    Player
                </a>
                <a href="{{ route('demo.switch', 'viewer') }}" class="px-2 py-0.5 rounded text-[11px] font-semibold transition {{ auth()->check() && auth()->user()->isViewer() ? 'bg-purple-500 text-gray-950 ring-1 ring-purple-400' : 'bg-gray-800 text-purple-400 hover:bg-gray-700' }}">
                    Viewer
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 w-full backdrop-blur-md bg-gray-900/90 border-b border-gray-800">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Identity -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-600 to-sky-400 flex items-center justify-center shadow-lg shadow-cyan-500/20 group-hover:shadow-cyan-500/40 transition-all">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-lg font-black tracking-wider text-white flex items-center gap-1">
                                LEAGUE<span class="text-cyan-400">HUB</span>
                            </span>
                            <span class="text-[10px] text-cyan-400/80 font-mono tracking-widest uppercase -mt-1">Championship OS</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation Links -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('home') ? 'text-cyan-400 bg-gray-800/80 shadow-sm' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            Home
                        </a>
                        <a href="{{ route('tournaments.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('tournaments.*') ? 'text-cyan-400 bg-gray-800/80 shadow-sm' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            Tournaments
                        </a>
                        <a href="{{ route('teams.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('teams.*') ? 'text-cyan-400 bg-gray-800/80 shadow-sm' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            Teams
                        </a>

                        @auth
                            <a href="{{ route('mytournaments') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('mytournaments') ? 'text-cyan-400 bg-gray-800/80 shadow-sm' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                                My Hub
                            </a>

                            @if(auth()->user()->isPlayer() || auth()->user()->isAdmin())
                                <a href="{{ route('mystats') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('mystats') ? 'text-cyan-400 bg-gray-800/80 shadow-sm' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                                    Player Analytics
                                </a>
                            @endif

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.*') ? 'text-cyan-400 bg-cyan-950/40 border border-cyan-800/50' : 'text-cyan-300 hover:text-cyan-200 hover:bg-gray-800/50' }}">
                                    Authority Console
                                </a>
                            @endif
                        @endauth

                        <a href="{{ route('news.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('news.*') ? 'text-cyan-400 bg-gray-800/80 shadow-sm' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            News & Incidents
                        </a>
                        <a href="{{ route('about') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('about') ? 'text-cyan-400 bg-gray-800/80 shadow-sm' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                            About
                        </a>
                    </div>
                </div>

                <!-- Right Side Actions: Theme Toggle, Notifications, User Menu -->
                <div class="flex items-center gap-3">
                    <!-- Dark / Light Mode Toggle Button -->
                    <button type="button" onclick="window.toggleDarkMode()" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 focus:outline-none transition" title="Toggle Theme">
                        <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                        <svg class="w-5 h-5 block dark:hidden text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>

                    @auth
                        <!-- Notification Bell Dropdown (Flowbite powered) -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 relative focus:outline-none transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-cyan-400 ring-2 ring-gray-900 animate-ping"></span>
                                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-cyan-400 ring-2 ring-gray-900"></span>
                                @endif
                            </button>

                            <!-- Notifications Dropdown Box -->
                            <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-80 sm:w-96 bg-gray-800 border border-gray-700 rounded-xl shadow-2xl py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-700 flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-gray-300">Notifications ({{ auth()->user()->unreadNotifications->count() }})</span>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs text-cyan-400 hover:underline">Mark all read</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto divide-y divide-gray-700/50">
                                    @forelse(auth()->user()->notifications->take(6) as $notification)
                                        <div class="p-3 hover:bg-gray-700/40 transition flex items-start gap-3 {{ $notification->read_at ? 'opacity-70' : 'bg-cyan-500/5' }}">
                                            <div class="mt-0.5 text-cyan-400 shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-semibold text-white truncate">{{ $notification->data['title'] ?? 'Platform Alert' }}</div>
                                                <p class="text-[11px] text-gray-300 line-clamp-2 mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                                                <div class="text-[10px] text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-xs text-gray-400">
                                            No notifications right now.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ userMenu: false }">
                            <button @click="userMenu = !userMenu" type="button" class="flex items-center gap-2 p-1 rounded-full hover:ring-2 hover:ring-cyan-500/50 transition">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-cyan-600 to-sky-400 flex items-center justify-center font-bold text-xs text-white uppercase shadow">
                                    {{ substr(auth()->user()->name, 0, 2) }}
                                </div>
                                <span class="hidden lg:block text-xs font-medium text-gray-200">{{ auth()->user()->name }}</span>
                                <span class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-semibold uppercase rounded bg-gray-800 text-cyan-400 border border-gray-700">
                                    {{ auth()->user()->role }}
                                </span>
                            </button>

                            <div x-show="userMenu" @click.outside="userMenu = false" x-cloak class="absolute right-0 mt-2 w-56 bg-gray-800 border border-gray-700 rounded-xl shadow-2xl py-1 z-50">
                                <div class="px-4 py-2 border-b border-gray-700 text-xs">
                                    <div class="font-bold text-white">{{ auth()->user()->name }}</div>
                                    <div class="text-gray-400 truncate">{{ auth()->user()->email }}</div>
                                    <div class="mt-1 text-[11px] text-cyan-400">Zone: {{ auth()->user()->areal_zone ?? 'Metro Central' }}</div>
                                </div>
                                <a href="{{ route('mytournaments') }}" class="block px-4 py-2 text-xs text-gray-300 hover:bg-gray-700 hover:text-white">Personal Hub</a>
                                @if(auth()->user()->isPlayer() || auth()->user()->isAdmin())
                                    <a href="{{ route('mystats') }}" class="block px-4 py-2 text-xs text-gray-300 hover:bg-gray-700 hover:text-white">Player Analytics & Health</a>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-xs text-cyan-400 hover:bg-gray-700 font-semibold">Authority Dashboard</a>
                                @endif
                                <div class="border-t border-gray-700 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-400 hover:bg-gray-700">Sign out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest Auth Buttons -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-medium text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                Log in
                            </a>
                            <a href="{{ route('register') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-cyan-500 to-sky-500 hover:from-cyan-400 hover:to-sky-400 text-gray-950 shadow-md shadow-cyan-500/20 transition">
                                Register
                            </a>
                        </div>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button data-collapse-toggle="mobile-menu" type="button" class="md:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div class="hidden md:hidden pb-4 pt-2 border-t border-gray-800" id="mobile-menu">
                <div class="flex flex-col space-y-1">
                    <a href="{{ route('home') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800">Home</a>
                    <a href="{{ route('tournaments.index') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800">Tournaments</a>
                    <a href="{{ route('teams.index') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800">Teams</a>
                    @auth
                        <a href="{{ route('mytournaments') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800">My Hub</a>
                        @if(auth()->user()->isPlayer() || auth()->user()->isAdmin())
                            <a href="{{ route('mystats') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800">Player Analytics</a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-semibold text-cyan-400 hover:bg-gray-800">Authority Console</a>
                        @endif
                    @endauth
                    <a href="{{ route('news.index') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800">News & Incidents</a>
                    <a href="{{ route('about') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800">About</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Flash Messages Container -->
    @if(session('success') || session('error') || session('info') || $errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="p-3.5 rounded-xl bg-cyan-950/40 border border-cyan-500/30 text-cyan-300 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-red-950/40 border border-red-500/30 text-red-300 text-sm">
                    <div class="font-semibold mb-1">Please correct the following:</div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs text-red-400">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content Slot -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 text-gray-400 text-xs py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                <span class="text-white font-bold tracking-wider">LEAGUEHUB PLATFORM</span>
                <span>&copy; {{ date('Y') }} All Rights Reserved.</span>
            </div>
            <div class="flex items-center gap-6 text-gray-400">
                <a href="{{ route('tournaments.index') }}" class="hover:text-cyan-400 transition">Tournaments</a>
                <a href="{{ route('teams.index') }}" class="hover:text-cyan-400 transition">Teams Directory</a>
                <a href="{{ route('news.index') }}" class="hover:text-cyan-400 transition">Match Incidents</a>
                <a href="{{ route('about') }}" class="hover:text-cyan-400 transition">Docs & Guidelines</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

