<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\User;
use App\Models\NewsArticle;
use Illuminate\View\View;

/**
 * HomeController
 *
 * Handles landing page presentation, including real-time match ticker data,
 * featured competitions, recent score lines, and league-wide metrics.
 */
class HomeController extends Controller
{
    /**
     * Display the primary public landing page.
     *
     * Aggregates active matches, upcoming fixtures, recent results,
     * featured tournaments, official news, and platform metrics.
     */
    public function index(): View
    {
        // ==========================================
        // 1. Live Match Retrieval
        // Query matches currently in-progress with associated relations
        // ==========================================
        $liveMatches = GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', 'live')
            ->orderBy('scheduled_at', 'desc')
            ->get();

        // ==========================================
        // 2. Real-Time Ticker Data Assembly
        // If no matches currently marked live, grab recent or upcoming for display in ticker
        // ==========================================
        $tickerMatchesQuery = $liveMatches->isNotEmpty()
            ? $liveMatches
            : GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
                ->orderByRaw("CASE WHEN status = 'live' THEN 1 WHEN status = 'upcoming' THEN 2 ELSE 3 END")
                ->orderBy('scheduled_at', 'asc')
                ->take(6)
                ->get();

        // Transform query results into structured ticker array for front-end presentation
        $tickerData = $tickerMatchesQuery->map(function ($m) {
            return [
                'id' => $m->id,
                'home_team' => $m->homeTeam->name,
                'away_team' => $m->awayTeam->name,
                'home_score' => $m->home_score,
                'away_score' => $m->away_score,
                'status' => $m->status,
                'stage' => ucfirst(str_replace('_', ' ', $m->stage)),
                'tournament' => $m->tournament->title ?? 'Exhibition',
                'scheduled_at' => $m->scheduled_at->format('M d, H:i'),
                'venue' => $m->venue,
            ];
        })->values()->toArray();

        // ==========================================
        // 3. Upcoming Fixtures & Recent Results
        // Retrieve upcoming matches sorted chronologically and finished matches sorted by recency
        // ==========================================
        $upcomingFixtures = GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', 'upcoming')
            ->orderBy('scheduled_at', 'asc')
            ->take(6)
            ->get();

        $recentResults = GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')
            ->take(4)
            ->get();

        // ==========================================
        // 4. Featured Tournaments & Official News
        // Gather open/ongoing tournaments and published articles
        // ==========================================
        $featuredTournaments = Tournament::withCount(['teams', 'matches'])
            ->whereIn('status', ['registration_open', 'in_progress'])
            ->take(3)
            ->get();

        $latestNews = NewsArticle::with(['author', 'match'])
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // ==========================================
        // 5. League Platform Overview Statistics
        // Aggregate total numbers of competitions, teams, players, and fixtures
        // ==========================================
        $stats = [
            'tournaments_count' => Tournament::count(),
            'active_tournaments_count' => Tournament::whereIn('status', ['registration_open', 'in_progress'])->count(),
            'teams_count' => Team::count(),
            'players_count' => User::where('role', 'player')->count(),
            'matches_count' => GameMatch::count(),
        ];

        // ==========================================
        // 6. View Response Rendering
        // Pass all compiled datasets to the home blade view
        // ==========================================
        return view('pages.home', compact(
            'liveMatches',
            'tickerData',
            'upcomingFixtures',
            'recentResults',
            'featuredTournaments',
            'latestNews',
            'stats'
        ));
    }
}
