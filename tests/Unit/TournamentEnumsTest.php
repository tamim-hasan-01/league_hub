<?php

use App\Enums\MatchStatus;
use App\Enums\PlayerRole;
use App\Enums\TournamentFormat;

test('tournament format enum defines expected formats and labels', function () {
    $formats = TournamentFormat::values();

    expect($formats)->toContain('single_elimination');
    expect($formats)->toContain('double_elimination');
    expect($formats)->toContain('round_robin');
    expect($formats)->toContain('group_stage');

    expect(TournamentFormat::SINGLE_ELIMINATION->label())->toBe('Single Elimination');
    expect(TournamentFormat::ROUND_ROBIN->label())->toBe('Round Robin');
});

test('match status enum provides valid statuses and badge colors', function () {
    expect(MatchStatus::values())->toContain('upcoming', 'live', 'completed', 'postponed', 'cancelled');
    expect(MatchStatus::LIVE->label())->toBe('Live');
    expect(MatchStatus::LIVE->badgeColor())->toBe('emerald');
    expect(MatchStatus::UPCOMING->badgeColor())->toBe('amber');
});

test('player role enum contains standard team positions', function () {
    expect(PlayerRole::values())->toContain('captain', 'player', 'substitute', 'goalkeeper');
    expect(PlayerRole::CAPTAIN->label())->toBe('Team Captain');
});
