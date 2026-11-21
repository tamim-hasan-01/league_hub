<x-app-layout>
    <div class="space-y-8" x-data="playerAnalytics()">
        <!-- Athlete Profile Hero Banner -->
        <section class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-slate-950 text-white shadow-[0_24px_60px_rgba(15,23,42,0.12)]">
            <div class="absolute right-0 top-0 h-full w-2/5 bg-[radial-gradient(circle_at_center,rgba(129,140,248,0.24),transparent_65%)]"></div>

            <div class="relative p-6 sm:p-8 lg:p-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="flex items-start sm:items-center gap-4 sm:gap-6">
                        <div class="flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center rounded-2xl bg-indigo-600 font-black text-white text-2xl sm:text-3xl shadow-lg shrink-0">
                            #{{ $profile->jersey_number ?? '10' }}
                        </div>

                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full border border-indigo-300/20 bg-indigo-300/10 px-3 py-1 text-xs font-bold text-indigo-200">
                                    {{ $profile->primary_sport }}
                                </span>
                                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-mono font-bold text-slate-200">
                                    Regional Rank #{{ $profile->areal_ranking }}
                                </span>
                                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                    Zone: {{ auth()->user()->areal_zone ?? 'Metro Central' }}
                                </span>
                            </div>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">{{ auth()->user()->name }}</h1>
                            <p class="text-xs text-slate-300">
                                Jersey: <span class="text-white font-bold">#{{ $profile->jersey_number }}</span>
                                &bull; Registered Teams: <span class="text-indigo-300 font-semibold">{{ $teams->pluck('name')->join(', ') ?: 'Free Agent' }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Health Status Badge & Quick Toggle -->
                    <div class="p-4 rounded-2xl border border-white/10 bg-white/5 backdrop-blur sm:max-w-xs w-full lg:self-center">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Current Health</span>
                            @if($profile->is_injured)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-400/10 text-rose-300 border border-rose-400/20 animate-pulse">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>
                                    INJURED (HOLD)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-400/10 text-emerald-300 border border-emerald-400/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                    ACTIVE &amp; FIT
                                </span>
                            @endif
                        </div>

                        @if($profile->is_injured && $profile->injury_notes)
                            <p class="text-[11px] text-rose-300 mb-2 italic">"{{ $profile->injury_notes }}"</p>
                        @endif

                        <!-- Health Toggle Button -->
                        <button data-modal-target="health-toggle-modal" data-modal-toggle="health-toggle-modal"
                            class="w-full py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white/10 border border-white/15 text-white hover:bg-white/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Update Health Status</span>
                        </button>
                    </div>
                </div>

                <!-- Career Stat Cards -->
                @php $stats = $profile->career_stats ?? []; @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/10 mt-8">
                    <div class="bg-slate-950/75 p-4 text-center">
                        <span class="text-slate-300 text-[11px] block uppercase font-bold tracking-wider">Form Rating</span>
                        <span class="text-3xl font-black text-indigo-300 mt-1 block tabular-nums">{{ $stats['form_rating'] ?? 8.1 }}</span>
                    </div>
                    <div class="bg-slate-950/75 p-4 text-center">
                        <span class="text-slate-300 text-[11px] block uppercase font-bold tracking-wider">Appearances</span>
                        <span class="text-3xl font-black text-white mt-1 block tabular-nums">{{ $stats['matches_played'] ?? 14 }}</span>
                    </div>
                    <div class="bg-slate-950/75 p-4 text-center">
                        <span class="text-slate-300 text-[11px] block uppercase font-bold tracking-wider">Contributions</span>
                        <span class="text-3xl font-black text-white mt-1 block tabular-nums">{{ $stats['goals_or_points'] ?? 11 }}</span>
                    </div>
                    <div class="bg-slate-950/75 p-4 text-center">
                        <span class="text-slate-300 text-[11px] block uppercase font-bold tracking-wider">MVP Awards</span>
                        <span class="text-3xl font-black text-amber-300 mt-1 block tabular-nums">{{ $stats['mvp_count'] ?? 3 }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Visual Analytics with Chart.js -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Form Curve Line Chart -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Performance</p>
                        <h2 class="mt-1 text-lg font-bold text-slate-950">Form Curve &amp; Rating Evolution</h2>
                        <p class="text-xs text-slate-500">Match-by-match performance index over recent fixtures</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        Peak: 8.5
                    </span>
                </div>

                <div class="h-64 relative">
                    <canvas id="formCurveChart"></canvas>
                </div>
            </div>

            <!-- Radar / Skill Distribution Chart -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Scouting</p>
                        <h2 class="mt-1 text-lg font-bold text-slate-950">Athletic &amp; Tactical Attributes</h2>
                        <p class="text-xs text-slate-500">Regional scouting evaluation benchmark</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        Tier 1 Elite
                    </span>
                </div>

                <div class="h-64 relative">
                    <canvas id="attributesRadarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Match Appearance History & Callups -->
        <div class="space-y-4">
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">Call-up history</p>
                    <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Match Appearance Logs &amp; Call-up History</h2>
                </div>
                <span class="text-xs font-semibold text-slate-400">{{ $invitations->count() }} records</span>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-[0_8px_24px_rgba(15,23,42,0.05)]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold text-[11px] border-b border-slate-200">
                            <tr>
                                <th class="p-4">Fixture</th>
                                <th class="p-4">Tournament / Stage</th>
                                <th class="p-4">Venue</th>
                                <th class="p-4">Kickoff</th>
                                <th class="p-4">Call-up Status</th>
                                <th class="p-4">Manager Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($invitations as $inv)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-bold text-slate-950">
                                        {{ $inv->match->homeTeam->name ?? 'Home' }} vs {{ $inv->match->awayTeam->name ?? 'Away' }}
                                    </td>
                                    <td class="p-4">
                                        <span class="font-semibold text-indigo-600">{{ $inv->match->tournament->title ?? 'Friendly Exhibition' }}</span>
                                        <span class="text-slate-400 block text-[11px]">({{ str_replace('_', ' ', $inv->match->stage) }})</span>
                                    </td>
                                    <td class="p-4 text-slate-600">{{ $inv->match->venue }}</td>
                                    <td class="p-4 font-mono font-medium text-slate-600">{{ $inv->match->scheduled_at->format('M d, H:i') }}</td>
                                    <td class="p-4">
                                        @if($inv->status === 'accepted')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                Accepted
                                            </span>
                                        @elseif($inv->status === 'declined')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                Declined
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                Pending Response
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-500 italic">{{ $inv->manager_notes ?? 'Regular squad callup' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center text-xs font-medium text-slate-500">
                                        No callup invitations found in player history.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Health Toggle Modal -->
    <div id="health-toggle-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-slate-950/60 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white border border-slate-200 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-950">Update Medical &amp; Fitness Status</h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" data-modal-hide="health-toggle-modal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>

                <form action="{{ route('mystats.toggleHealth') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-600">Status</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer p-3 rounded-xl border border-slate-200 text-center flex items-center justify-center gap-2 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 has-[:checked]:ring-1 has-[:checked]:ring-indigo-600">
                                <input type="radio" name="is_injured" value="0" {{ !$profile->is_injured ? 'checked' : '' }} class="sr-only">
                                <span class="text-emerald-700 font-bold text-xs flex items-center gap-1.5">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Active &amp; Fit
                                </span>
                            </label>
                            <label class="cursor-pointer p-3 rounded-xl border border-slate-200 text-center flex items-center justify-center gap-2 transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 has-[:checked]:ring-1 has-[:checked]:ring-rose-500">
                                <input type="radio" name="is_injured" value="1" {{ $profile->is_injured ? 'checked' : '' }} class="sr-only">
                                <span class="text-rose-700 font-bold text-xs flex items-center gap-1.5">
                                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                    Injured (Hold)
                                </span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="injury_notes" class="block mb-1.5 text-xs font-bold text-slate-600">Medical Notes / Physio Report</label>
                        <textarea name="injury_notes" id="injury_notes" rows="3" class="block w-full rounded-xl border border-slate-200 bg-white p-3 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Specify injury description (e.g., Hamstring grade 1, recovery ETA 10 days)...">{{ $profile->injury_notes }}</textarea>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-600">
                        Update Health Record
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function playerAnalytics() {
            return {
                init() {
                    this.renderFormChart();
                    this.renderRadarChart();
                },
                renderFormChart() {
                    const ctx = document.getElementById('formCurveChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                label: 'Match Rating (Out of 10)',
                                data: @json($chartRatings),
                                borderColor: '#4F46E5',
                                backgroundColor: 'rgba(79, 70, 229, 0.08)',
                                tension: 0.35,
                                fill: true,
                                pointBackgroundColor: '#4F46E5',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    min: 5,
                                    max: 10,
                                    grid: { color: 'rgba(226, 232, 240, 0.8)' },
                                    ticks: { color: '#64748B', font: { size: 11 } }
                                },
                                x: {
                                    grid: { color: 'rgba(226, 232, 240, 0.8)' },
                                    ticks: { color: '#64748B', font: { size: 11 } }
                                }
                            }
                        }
                    });
                },
                renderRadarChart() {
                    const ctx = document.getElementById('attributesRadarChart');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'radar',
                        data: {
                            labels: ['Pace / Speed', 'Technique', 'Stamina', 'Teamwork', 'Defense / Map IQ', 'Mental Composure'],
                            datasets: [{
                                label: 'Attribute Level',
                                data: [84, 88, 80, 92, 74, 86],
                                borderColor: '#6366F1',
                                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                                pointBackgroundColor: '#4F46E5',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                r: {
                                    angleLines: { color: 'rgba(226, 232, 240, 0.9)' },
                                    grid: { color: 'rgba(226, 232, 240, 0.9)' },
                                    pointLabels: { color: '#334155', font: { size: 11, weight: '600' } },
                                    ticks: { display: false, max: 100 }
                                }
                            }
                        }
                    });
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

