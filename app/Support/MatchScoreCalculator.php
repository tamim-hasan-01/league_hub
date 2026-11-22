<?php

namespace App\Support;

/**
 * MatchScoreCalculator
 *
 * Dedicated calculation utility for tournament league tables,
 * match outcomes, points attribution, and goal difference metrics.
 */
class MatchScoreCalculator
{
    public const OUTCOME_HOME_WIN = 'home_win';
    public const OUTCOME_AWAY_WIN = 'away_win';
    public const OUTCOME_DRAW = 'draw';

    /**
     * Compute total tournament league points for a given record.
     */
    public static function calculatePoints(
        int $wins,
        int $draws,
        int $losses = 0,
        int $winPoints = 3,
        int $drawPoints = 1
    ): int {
        return max(0, ($wins * $winPoints) + ($draws * $drawPoints));
    }

    /**
     * Determine outcome of a fixture based on final scoreline.
     */
    public static function determineOutcome(int $homeScore, int $awayScore): string
    {
        if ($homeScore > $awayScore) {
            return self::OUTCOME_HOME_WIN;
        }

        if ($awayScore > $homeScore) {
            return self::OUTCOME_AWAY_WIN;
        }

        return self::OUTCOME_DRAW;
    }

    /**
     * Format a score pair into a clean human-readable string.
     */
    public static function formatScore(?int $homeScore, ?int $awayScore): string
    {
        if ($homeScore === null || $awayScore === null) {
            return 'vs';
        }

        return "{$homeScore} - {$awayScore}";
    }

    /**
     * Compute goal difference from goals scored and goals conceded.
     */
    public static function computeGoalDifference(int $goalsFor, int $goalsAgainst): int
    {
        return $goalsFor - $goalsAgainst;
    }

    /**
     * Sort a list of team standing records by points, goal difference, and goals scored.
     *
     * Each item expected to contain: 'points', 'goal_difference', 'goals_for'.
     */
    public static function sortStandings(array $standings): array
    {
        usort($standings, function ($a, $b) {
            // Sort by points descending
            if (($a['points'] ?? 0) !== ($b['points'] ?? 0)) {
                return ($b['points'] ?? 0) <=> ($a['points'] ?? 0);
            }

            // Tie-break by goal difference descending
            if (($a['goal_difference'] ?? 0) !== ($b['goal_difference'] ?? 0)) {
                return ($b['goal_difference'] ?? 0) <=> ($a['goal_difference'] ?? 0);
            }

            // Tie-break by goals scored descending
            return ($b['goals_for'] ?? 0) <=> ($a['goals_for'] ?? 0);
        });

        return array_values($standings);
    }
}
