<x-app-layout>
    <div class="space-y-10 max-w-5xl mx-auto">
        <!-- Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gray-800 border border-gray-700 p-6 sm:p-10 shadow-2xl">
            <div class="space-y-3 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold uppercase tracking-wider">
                    Official League Platform Documentation
                </div>
                <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">About LeagueHub OS</h1>
                <p class="text-sm sm:text-base text-gray-300 leading-relaxed">
                    A responsive, high-performance Sports League and Tournament Management Web Application engineered strictly within the modern Laravel ecosystem and front-end tooling.
                </p>
            </div>
        </div>

        <!-- 3 Core Pillars -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center font-bold text-lg">
                    🎯
                </div>
                <h3 class="text-lg font-bold text-white">Mission Statement</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Empower sporting organizations, grassroots tournaments, esports circuits, and collegiate leagues to manage brackets, rosters, contracts, and score lines with zero friction.
                </p>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center font-bold text-lg">
                    ⚖️
                </div>
                <h3 class="text-lg font-bold text-white">Sports Regulations</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Strict adherence to fair-play policies, transparent disciplinary review logs for controversial match incidents, and real-time medical hold compliance for injured athletes.
                </p>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-lg">
                    ⚡
                </div>
                <h3 class="text-lg font-bold text-white">Real-Time Architecture</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Powered by Laravel Broadcast events (`MatchScoreUpdated`), instant score broadcasts, live radar tickers, and automated knockout stage advancement algorithms.
                </p>
            </div>
        </div>

        <!-- Architecture Breakdown -->
        <div class="bg-gray-800 border border-gray-700 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <span>🛠️</span>
                <span>Technology Stack & Architecture Matrix</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-4 bg-gray-900/60 rounded-xl border border-gray-700/60 space-y-2">
                    <span class="text-cyan-400 font-bold uppercase block">Backend Architecture</span>
                    <ul class="space-y-1.5 text-gray-300">
                        <li>&bull; <strong class="text-white">Laravel 12/13 Framework</strong> running PHP 8.5+</li>
                        <li>&bull; <strong class="text-white">Role-Based Access Control (RBAC)</strong>: Team Manager, Player, Viewer, League Admin</li>
                        <li>&bull; <strong class="text-white">Services</strong>: TournamentBracketService (seeding & progression), RankingService</li>
                        <li>&bull; <strong class="text-white">Events & Notifications</strong>: MatchScoreUpdated broadcast event, Database notifications</li>
                    </ul>
                </div>

                <div class="p-4 bg-gray-900/60 rounded-xl border border-gray-700/60 space-y-2">
                    <span class="text-sky-400 font-bold uppercase block">Frontend Tooling & Styling</span>
                    <ul class="space-y-1.5 text-gray-300">
                        <li>&bull; <strong class="text-white">Tailwind CSS v4</strong> with dark mode class strategy</li>
                        <li>&bull; <strong class="text-white">Flowbite UI</strong>: Responsive navbar, modals, badges, inputs</li>
                        <li>&bull; <strong class="text-white">Alpine.js</strong>: Reactive live score ticker, steppers, tab routing</li>
                        <li>&bull; <strong class="text-white">Chart.js</strong>: Form curves, rating evolution, radar skill distribution</li>
                    </ul>
                </div>
            </div>

            <!-- RBAC Matrix -->
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-gray-200 uppercase tracking-wider">Role Permissions Matrix</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-gray-700 rounded-xl overflow-hidden">
                        <thead class="bg-gray-900 text-gray-400 uppercase font-bold border-b border-gray-700">
                            <tr>
                                <th class="p-3">Role</th>
                                <th class="p-3">Primary Responsibilities</th>
                                <th class="p-3">Dedicated Endpoints</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/60 text-gray-300">
                            <tr>
                                <td class="p-3 font-bold text-cyan-400">League Admin</td>
                                <td class="p-3">Full authority over tournaments, brackets, live scoring console, application approvals, medical clearances, and disciplinary filings.</td>
                                <td class="p-3 font-mono text-gray-400">/admin/dashboard</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-sky-400">Team Manager</td>
                                <td class="p-3">Manage persistent team rosters, recruit available players, propose friendly matches, submit tournament applications.</td>
                                <td class="p-3 font-mono text-gray-400">/teams, /mytournaments</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-emerald-400">Player</td>
                                <td class="p-3">Track visual form curves & radar stats, respond to match call-up contracts (Accept/Decline), toggle active vs injured status.</td>
                                <td class="p-3 font-mono text-gray-400">/mystats, /mytournaments</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-bold text-purple-400">Viewer</td>
                                <td class="p-3">Follow multi-sport tournament brackets, bookmark favorite matches, receive real-time notifications for kickoff & results.</td>
                                <td class="p-3 font-mono text-gray-400">/, /tournaments, /mytournaments</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

