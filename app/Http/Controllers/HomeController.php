<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\User;
use App\Models\NewsArticle;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $liveMatches = GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->where('status', 'live')
            ->orderBy('scheduled_at', 'desc')
            ->get();

        // If no matches currently marked live, grab recent or upcoming for display in ticker
        $tickerMatchesQuery = $liveMatches->isNotEmpty()
            ? $liveMatches
            : GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
                ->orderByRaw("CASE WHEN status = 'live' THEN 1 WHEN status = 'upcoming' THEN 2 ELSE 3 END")
                ->orderBy('scheduled_at', 'asc')
                ->take(6)
                ->get();

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

        $featuredTournaments = Tournament::withCount(['teams', 'matches'])
            ->whereIn('status', ['registration_open', 'in_progress'])
            ->take(3)
            ->get();

        $latestNews = NewsArticle::with(['author', 'match'])
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        $stats = [
            'tournaments_count' => Tournament::count(),
            'active_tournaments_count' => Tournament::whereIn('status', ['registration_open', 'in_progress'])->count(),
            'teams_count' => Team::count(),
            'players_count' => User::where('role', 'player')->count(),
            'matches_count' => GameMatch::count(),
        ];

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

