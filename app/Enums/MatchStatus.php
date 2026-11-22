<?php

namespace App\Enums;

enum MatchStatus: string
{
    case UPCOMING = 'upcoming';
    case LIVE = 'live';
    case COMPLETED = 'completed';
    case POSTPONED = 'postponed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::UPCOMING => 'Upcoming',
            self::LIVE => 'Live',
            self::COMPLETED => 'Completed',
            self::POSTPONED => 'Postponed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::UPCOMING => 'amber',
            self::LIVE => 'emerald',
            self::COMPLETED => 'slate',
            self::POSTPONED => 'yellow',
            self::CANCELLED => 'rose',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
