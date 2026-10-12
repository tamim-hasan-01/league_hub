<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use Illuminate\Http\JsonResponse;

class LiveMatchController extends Controller
{
    public function ticker(): JsonResponse
    {
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

        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'matches' => $matches,
        ]);
    }

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

