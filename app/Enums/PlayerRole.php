<?php

namespace App\Enums;

enum PlayerRole: string
{
    case CAPTAIN = 'captain';
    case PLAYER = 'player';
    case SUBSTITUTE = 'substitute';
    case GOALKEEPER = 'goalkeeper';

    public function label(): string
    {
        return match ($this) {
            self::CAPTAIN => 'Team Captain',
            self::PLAYER => 'Starting Player',
            self::SUBSTITUTE => 'Substitute',
            self::GOALKEEPER => 'Goalkeeper',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
