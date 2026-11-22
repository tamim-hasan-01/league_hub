<?php

use App\Support\MatchScoreCalculator;

test('calculates standard points for league record', function () {
    $points = MatchScoreCalculator::calculatePoints(wins: 5, draws: 2, losses: 1);
    expect($points)->toBe(17);
});

test('calculates points with custom point weights', function () {
    $points = MatchScoreCalculator::calculatePoints(wins: 3, draws: 1, losses: 0, winPoints: 2, drawPoints: 1);
    expect($points)->toBe(7);
});

test('determines correct match outcome from scores', function () {
    expect(MatchScoreCalculator::determineOutcome(3, 1))->toBe(MatchScoreCalculator::OUTCOME_HOME_WIN);
    expect(MatchScoreCalculator::determineOutcome(0, 2))->toBe(MatchScoreCalculator::OUTCOME_AWAY_WIN);
    expect(MatchScoreCalculator::determineOutcome(2, 2))->toBe(MatchScoreCalculator::OUTCOME_DRAW);
});

test('formats scoreline into readable string', function () {
    expect(MatchScoreCalculator::formatScore(2, 1))->toBe('2 - 1');
    expect(MatchScoreCalculator::formatScore(null, null))->toBe('vs');
});

test('computes goal difference correctly', function () {
    expect(MatchScoreCalculator::computeGoalDifference(10, 4))->toBe(6);
    expect(MatchScoreCalculator::computeGoalDifference(2, 5))->toBe(-3);
});

test('sorts league standings by points, goal difference, and goals scored', function () {
    $rawStandings = [
        ['name' => 'Team C', 'points' => 9, 'goal_difference' => 2, 'goals_for' => 8],
        ['name' => 'Team A', 'points' => 12, 'goal_difference' => 5, 'goals_for' => 11],
        ['name' => 'Team B', 'points' => 9, 'goal_difference' => 4, 'goals_for' => 7],
    ];

    $sorted = MatchScoreCalculator::sortStandings($rawStandings);

    expect($sorted[0]['name'])->toBe('Team A');
    expect($sorted[1]['name'])->toBe('Team B');
    expect($sorted[2]['name'])->toBe('Team C');
});
