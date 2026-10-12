<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'home_team_id',
        'away_team_id',
        'stage',
        'scheduled_at',
        'venue',
        'status',
        'home_score',
        'away_score',
        'match_data',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'match_data' => 'array',
            'home_score' => 'integer',
            'away_score' => 'integer',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(MatchInvitation::class, 'match_id');
    }

    public function newsArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class, 'match_id');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function isFriendly(): bool
    {
        return is_null($this->tournament_id);
    }
}

