<?php

namespace App\Enums;

enum TournamentFormat: string
{
    case SINGLE_ELIMINATION = 'single_elimination';
    case DOUBLE_ELIMINATION = 'double_elimination';
    case ROUND_ROBIN = 'round_robin';
    case GROUP_STAGE = 'group_stage';

    public function label(): string
    {
        return match ($this) {
            self::SINGLE_ELIMINATION => 'Single Elimination',
            self::DOUBLE_ELIMINATION => 'Double Elimination',
            self::ROUND_ROBIN => 'Round Robin',
            self::GROUP_STAGE => 'Group Stage Knockout',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
