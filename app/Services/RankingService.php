<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;

/**
 * RankingService
 *
 * Implements competitive rating and form calculations for both clubs and individual players:
 * - Computes team Elo/league standing points from cumulative match results
 * - Updates individual player match logs, recent form averages, and areal rankings
 */
class RankingService
{
    /**
     * Recalculate ranking scores for teams based on match outcomes.
     *
     * Computes points using base metric of 1000 + (wins * 35) + (draws * 10) - (losses * 15).
     */
    public function recalculateTeamRankings(): void
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            $record = $team->record;

            // ==========================================
            // Team Standing Formula Calculation
            // Base score 1000 + (wins * 35) + (draws * 10) - (losses * 15)
            // Minimum lower threshold clamped to 100
            // ==========================================
            $points = 1000 + ($record['wins'] * 35) + ($record['draws'] * 10) - ($record['losses'] * 15);
            $points = max(100, $points);

            $team->update(['areal_ranking' => $points]);
        }
    }

    /**
     * Update player statistics after a match.
     *
     * Ingests match deltas (goals, assists, MVP status) and recalibrates the rolling form rating.
     */
    public function updatePlayerStats(User $player, array $statsDelta): void
    {
        $profile = $player->playerProfile;
        if (!$profile) return;

        // ==========================================
        // 1. Initialize Default Career Record
        // ==========================================
        $stats = $profile->career_stats ?? [
            'matches_played' => 0,
            'goals_or_points' => 0,
            'assists' => 0,
            'mvp_count' => 0,
            'form_rating' => 7.5,
            'recent_ratings' => [7.2, 7.5, 7.8, 8.0, 7.6]
        ];

        // ==========================================
        // 2. Accumulate Match Deltas
        // ==========================================
        $stats['matches_played'] = ($stats['matches_played'] ?? 0) + ($statsDelta['matches_played'] ?? 1);
        $stats['goals_or_points'] = ($stats['goals_or_points'] ?? 0) + ($statsDelta['goals_or_points'] ?? 0);
        $stats['assists'] = ($stats['assists'] ?? 0) + ($statsDelta['assists'] ?? 0);
        $stats['mvp_count'] = ($stats['mvp_count'] ?? 0) + ($statsDelta['mvp_count'] ?? 0);

        // ==========================================
        // 3. Rolling Form Rating Calculation
        // Keep a rolling window of recent ratings (max 8 matches)
        // ==========================================
        if (isset($statsDelta['rating'])) {
            $recent = $stats['recent_ratings'] ?? [];
            $recent[] = (float) $statsDelta['rating'];
            if (count($recent) > 8) {
                array_shift($recent);
            }
            $stats['recent_ratings'] = $recent;
            $stats['form_rating'] = round(array_sum($recent) / count($recent), 1);
        }

        // ==========================================
        // 4. Persist Updated Profile Metrics
        // ==========================================
        $profile->update([
            'career_stats' => $stats,
            'areal_ranking' => max(1, $profile->areal_ranking - ($statsDelta['ranking_boost'] ?? 0))
        ]);
    }
}
