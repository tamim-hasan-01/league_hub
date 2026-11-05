<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use Illuminate\Http\JsonResponse;

/**
 * LiveMatchController
 *
 * RESTful API endpoints powering real-time match tickers, polling widgets,
 * and live fixture details for mobile clients and frontend dashboards.
 */
class LiveMatchController extends Controller
{
    /**
     * Retrieve serialized live and upcoming matches for the ticker banner.
     *
     * Prioritizes live matches, followed by upcoming fixtures and recent results.
     */
    public function ticker(): JsonResponse
    {
        // ==========================================
        // 1. Query Top Fixtures for Ticker Display
        // Order by live status first, then upcoming, then scheduled time
        // ==========================================
        $matches = GameMatch::with(['homeTeam', 'awayTeam', 'tournament'])
            ->orderByRaw("CASE WHEN status = 'live' THEN 1 WHEN status = 'upcoming' THEN 2 ELSE 3 END")
            ->orderBy('scheduled_at')
            ->take(8)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'home_team' => $m->homeTeam->name,
                    'away_team' => $m->awayTeam->name,
                    'home_score' => $m->home_score,
                    'away_score' => $m->away_score,
                    'status' => $m->status,
                    'stage' => ucfirst(str_replace('_', ' ', $m->stage)),
                    'tournament' => $m->tournament->title ?? 'Friendly Cup',
                    'scheduled_at' => $m->scheduled_at->format('M d, H:i'),
                    'venue' => $m->venue,
                ];
            });

        // ==========================================
        // 2. Return JSON Response With Current Timestamp
        // ==========================================
        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'matches' => $matches,
        ]);
    }

    /**
     * Return detailed data payload for a specific match fixture.
     *
     * Includes teams, current scoreline, timeline incidents, and venue metadata.
     */
    public function show(GameMatch $match): JsonResponse
    {
        return response()->json([
            'id' => $match->id,
            'home_team' => $match->homeTeam->name,
            'away_team' => $match->awayTeam->name,
            'home_score' => $match->home_score,
            'away_score' => $match->away_score,
            'status' => $match->status,
            'match_data' => $match->match_data,
            'scheduled_at' => $match->scheduled_at->toIso8601String(),
            'venue' => $match->venue,
        ]);
    }
}
