<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;

class RankingService
{
    /**
     * Recalculate ranking scores for teams based on match outcomes.
     */
    public function recalculateTeamRankings(): void
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            $record = $team->record;
            // Base score 1000 + (wins * 35) + (draws * 10) - (losses * 15)
            $points = 1000 + ($record['wins'] * 35) + ($record['draws'] * 10) - ($record['losses'] * 15);
            $points = max(100, $points);

            $team->update(['areal_ranking' => $points]);
        }
    }

    /**
     * Update player statistics after a match.
     */
    public function updatePlayerStats(User $player, array $statsDelta): void
    {
        $profile = $player->playerProfile;
        if (!$profile) return;

        $stats = $profile->career_stats ?? [
            'matches_played' => 0,
            'goals_or_points' => 0,
            'assists' => 0,
            'mvp_count' => 0,
            'form_rating' => 7.5,
            'recent_ratings' => [7.2, 7.5, 7.8, 8.0, 7.6]
        ];

        $stats['matches_played'] = ($stats['matches_played'] ?? 0) + ($statsDelta['matches_played'] ?? 1);
        $stats['goals_or_points'] = ($stats['goals_or_points'] ?? 0) + ($statsDelta['goals_or_points'] ?? 0);
        $stats['assists'] = ($stats['assists'] ?? 0) + ($statsDelta['assists'] ?? 0);
        $stats['mvp_count'] = ($stats['mvp_count'] ?? 0) + ($statsDelta['mvp_count'] ?? 0);

        if (isset($statsDelta['rating'])) {
            $recent = $stats['recent_ratings'] ?? [];
            $recent[] = (float) $statsDelta['rating'];
            if (count($recent) > 8) {
                array_shift($recent);
            }
            $stats['recent_ratings'] = $recent;
            $stats['form_rating'] = round(array_sum($recent) / count($recent), 1);
        }

        $profile->update([
            'career_stats' => $stats,
            'areal_ranking' => max(1, $profile->areal_ranking - ($statsDelta['ranking_boost'] ?? 0))
        ]);
    }
}

