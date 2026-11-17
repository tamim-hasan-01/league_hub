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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Form Curve Line Chart -->
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">Form Curve & Rating Evolution</h2>
                        <p class="text-xs text-gray-400">Match-by-match performance index over recent fixtures</p>
                    </div>
                    <span class="px-2.5 py-1 rounded text-xs font-mono font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                        Peak: 8.5
                    </span>
                </div>

                <div class="h-64 relative">
                    <canvas id="formCurveChart"></canvas>
                </div>
            </div>

            <!-- Radar / Skill Distribution Chart -->
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">Athletic & Tactical Attributes</h2>
                        <p class="text-xs text-gray-400">Regional scouting evaluation benchmark</p>
                    </div>
                    <span class="px-2.5 py-1 rounded text-xs font-mono font-bold bg-sky-500/10 text-sky-400 border border-sky-500/30">
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
            <h2 class="text-xl font-bold text-white">Match Appearance Logs & Call-up History</h2>

            <div class="bg-gray-800 border border-gray-700 rounded-2xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-900/80 text-gray-400 uppercase tracking-wider font-semibold border-b border-gray-700">
                            <tr>
                                <th class="p-3.5">Fixture</th>
                                <th class="p-3.5">Tournament / Stage</th>
                                <th class="p-3.5">Venue</th>
                                <th class="p-3.5">Kickoff</th>
                                <th class="p-3.5">Call-up Status</th>
                                <th class="p-3.5">Manager Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/60 text-gray-300">
                            @forelse($invitations as $inv)
                                <tr class="hover:bg-gray-750 transition">
                                    <td class="p-3.5 font-bold text-white">
                                        {{ $inv->match->homeTeam->name ?? 'Home' }} vs {{ $inv->match->awayTeam->name ?? 'Away' }}
                                    </td>
                                    <td class="p-3.5">
                                        <span class="text-cyan-400">{{ $inv->match->tournament->title ?? 'Friendly Exhibition' }}</span>
                                        <span class="text-gray-500 block text-[11px]">({{ str_replace('_', ' ', $inv->match->stage) }})</span>
                                    </td>
                                    <td class="p-3.5">{{ $inv->match->venue }}</td>
                                    <td class="p-3.5 font-mono text-gray-400">{{ $inv->match->scheduled_at->format('M d, H:i') }}</td>
                                    <td class="p-3.5">
                                        @if($inv->status === 'accepted')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                Accepted
                                            </span>
                                        @elseif($inv->status === 'declined')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                                Declined
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                                Pending Response
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-gray-400 italic">{{ $inv->manager_notes ?? 'Regular squad callup' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500">
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
    <div id="health-toggle-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-700">
                    <h3 class="text-base font-bold text-white">Update Medical & Fitness Status</h3>
                    <button type="button" class="text-gray-400 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" data-modal-hide="health-toggle-modal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>

                <form action="{{ route('mystats.toggleHealth') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-2 text-xs font-semibold text-gray-300 uppercase">Status</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer p-3 rounded-xl border border-gray-700 text-center flex items-center justify-center gap-2 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10 has-[:checked]:text-white">
                                <input type="radio" name="is_injured" value="0" {{ !$profile->is_injured ? 'checked' : '' }} class="sr-only">
                                <span class="text-emerald-400 font-bold text-xs">Active & Fit</span>
                            </label>
                            <label class="cursor-pointer p-3 rounded-xl border border-gray-700 text-center flex items-center justify-center gap-2 has-[:checked]:border-red-500 has-[:checked]:bg-red-500/10 has-[:checked]:text-white">
                                <input type="radio" name="is_injured" value="1" {{ $profile->is_injured ? 'checked' : '' }} class="sr-only">
                                <span class="text-red-400 font-bold text-xs">Injured (Medical Hold)</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="injury_notes" class="block mb-1 text-xs font-medium text-gray-300">Medical Notes / Physio Report</label>
                        <textarea name="injury_notes" id="injury_notes" rows="3" class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg block w-full p-2.5" placeholder="Specify injury description (e.g., Hamstring grade 1, recovery ETA 10 days)...">{{ $profile->injury_notes }}</textarea>
                    </div>

                    <button type="submit" class="w-full text-gray-950 bg-gradient-to-r from-cyan-500 to-sky-400 hover:from-cyan-400 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition">
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
                                borderColor: '#06B6D4',
                                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                                tension: 0.35,
                                fill: true,
                                pointBackgroundColor: '#38BDF8',
                                pointBorderColor: '#fff',
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
                                    grid: { color: 'rgba(75, 85, 99, 0.2)' },
                                    ticks: { color: '#9CA3AF' }
                                },
                                x: {
                                    grid: { color: 'rgba(75, 85, 99, 0.2)' },
                                    ticks: { color: '#9CA3AF' }
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
                                borderColor: '#38BDF8',
                                backgroundColor: 'rgba(56, 189, 248, 0.2)',
                                pointBackgroundColor: '#06B6D4',
                                pointBorderColor: '#fff',
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
                                    angleLines: { color: 'rgba(75, 85, 99, 0.3)' },
                                    grid: { color: 'rgba(75, 85, 99, 0.3)' },
                                    pointLabels: { color: '#D1D5DB', font: { size: 10 } },
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

