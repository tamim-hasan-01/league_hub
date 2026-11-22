<x-app-layout>
    <div class="space-y-12">
        <section class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-slate-950 px-6 py-10 text-white sm:px-10 lg:px-12 lg:py-14">
            <div class="absolute right-0 top-0 h-full w-1/2 bg-[radial-gradient(circle_at_center,rgba(129,140,248,0.28),transparent_68%)]"></div>
            <div class="relative max-w-3xl">
                <p class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-indigo-200">
                    About LeagueHub
                </p>
                <h1 class="mt-5 text-4xl font-black leading-[1.08] tracking-tight sm:text-5xl">
                    One platform for every <span class="text-indigo-300">competition.</span>
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-slate-300">
                    LeagueHub is a sports and tournament management platform designed to make registration, team operations, match scheduling, live scoring, rankings, and league communication clear and accessible.
                </p>
                <div class="mt-8 grid max-w-2xl grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach([
                        ['Multi-sport', 'One workspace'],
                        ['Live matches', 'Instant updates'],
                        ['Role-based', 'Secure access'],
                        ['Responsive', 'Every screen'],
                    ] as [$title, $detail])
                        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                            <p class="font-bold text-white">{{ $title }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $detail }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid items-center gap-8 lg:grid-cols-[1.15fr_0.85fr]">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Our purpose</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Built to organize sport better</h2>
                <p class="mt-5 text-base leading-8 text-slate-600">
                    Organizing a tournament should not mean scattered spreadsheets, inconsistent score records, or unclear communication. LeagueHub brings organizers, team managers, players, and spectators into one structured ecosystem.
                </p>
                <p class="mt-4 text-base leading-8 text-slate-600">
                    From creating a competition and registering teams to generating brackets and updating live results, the platform keeps every participant informed while preserving a clear and professional competition record.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['01', 'Tournament registration', 'Manage entries, applications, stages, and competition formats.'],
                    ['02', 'Team operations', 'Maintain rosters, managers, player records, and team activity.'],
                    ['03', 'Match visibility', 'Follow fixtures, results, scores, venues, and live match status.'],
                    ['04', 'League integrity', 'Use clear roles, news, incidents, and authoritative controls.'],
                ] as [$number, $title, $description])
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_10px_28px_rgba(15,23,42,0.05)]">
                        <span class="text-xs font-black tracking-[0.16em] text-indigo-600">{{ $number }}</span>
                        <h3 class="mt-3 font-bold text-slate-950">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="space-y-6">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold text-indigo-600">Platform capabilities</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Everything needed to run a competition</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach([
                    ['Tournaments', 'Create competitions, open registration, select formats, and organize tournament stages.'],
                    ['Team rosters', 'Maintain team identity, managers, player assignments, and competitive records.'],
                    ['Live scores', 'Publish match scores and keep fixtures and result cards synchronized.'],
                    ['Bracket management', 'Generate and progress knockout brackets through competition stages.'],
                    ['News & incidents', 'Share official announcements and document disciplinary or match incidents.'],
                    ['Player analytics', 'Track fitness, rankings, career statistics, recent form, and injury status.'],
                ] as [$title, $description])
                    <article class="rounded-2xl border border-slate-200 bg-white p-6">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-sm font-black text-indigo-700">
                            {{ strtoupper(substr($title, 0, 1)) }}
                        </span>
                        <h3 class="mt-5 text-lg font-bold text-slate-950">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-500">{{ $description }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
            <div class="grid lg:grid-cols-[0.75fr_1.25fr]">
                <div class="bg-slate-950 p-8 text-white sm:p-10">
                    <p class="text-sm font-semibold text-indigo-300">Technology</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight">Designed for reliable competition data</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300">
                        LeagueHub combines a structured Laravel backend with a responsive Tailwind interface, reactive Alpine components, and clear role-based workflows.
                    </p>
                </div>
                <div class="grid gap-px bg-slate-200 sm:grid-cols-2">
                    @foreach([
                        ['Laravel', 'Server-side application architecture, routing, authentication, and Eloquent data models.'],
                        ['Blade & Tailwind', 'Reusable templates and a responsive, consistent light-theme interface.'],
                        ['Alpine.js', 'Fast interactive states for navigation, account menus, and live match displays.'],
                        ['Chart.js', 'Visual player form, career statistics, and performance information.'],
                    ] as [$title, $description])
                        <article class="bg-white p-6 sm:p-8">
                            <h3 class="font-bold text-slate-950">{{ $title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold text-indigo-600">Development team</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Built by BAUST CSE students</h2>
                </div>
                <a href="https://github.com/tamim-hasan-01/league_hub" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm hover:border-indigo-200 hover:text-indigo-600">
                    View LeagueHub repository
                </a>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                @foreach($developers as $developer)
                    <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)]">
                        <div class="relative h-64 overflow-hidden bg-slate-100">
                            <img src="{{ asset($developer['image']) }}" alt="{{ $developer['name'] }}"
                                 class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-[1.02]">
                            <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-slate-950/40 to-transparent"></div>
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-indigo-600">{{ $developer['role'] }}</p>
                            <h3 class="mt-2 text-xl font-black text-slate-950">{{ $developer['name'] }}</h3>
                            <p class="mt-1 text-sm font-semibold text-slate-500">{{ $developer['year'] }}</p>
                            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                <span class="block text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Student ID</span>
                                <span class="mt-1 block font-mono text-sm font-bold text-slate-900">{{ $developer['student_id'] }}</span>
                            </div>
                            <p class="mt-4 min-h-24 text-sm leading-7 text-slate-500">{{ $developer['summary'] }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($developer['skills'] as $skill)
                                    <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $skill }}</span>
                                @endforeach
                            </div>
                            <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                                <a href="{{ $developer['github'] }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-3.5 py-2.5 text-sm font-bold text-white hover:bg-indigo-600">
                                    GitHub
                                </a>
                                @if($developer['email'])
                                    <a href="mailto:{{ $developer['email'] }}"
                                       class="inline-flex items-center rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm font-bold text-slate-700 hover:border-indigo-200 hover:text-indigo-600">
                                        Email
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="rounded-3xl border border-indigo-100 bg-indigo-50 px-6 py-8 sm:px-10">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-indigo-700">Explore LeagueHub</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-950">Discover tournaments, teams, and live competition</h2>
                </div>
                <a href="{{ route('tournaments.index') }}"
                   class="inline-flex shrink-0 items-center justify-center rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-indigo-600">
                    Browse tournaments
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
