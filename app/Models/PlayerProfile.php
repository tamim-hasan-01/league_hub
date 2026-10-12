<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jersey_number',
        'primary_sport',
        'is_injured',
        'injury_notes',
        'areal_ranking',
        'career_stats',
    ];

    protected function casts(): array
    {
        return [
            'is_injured' => 'boolean',
            'career_stats' => 'array',
            'areal_ranking' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

