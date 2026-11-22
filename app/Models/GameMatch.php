<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * GameMatch Model
 *
 * Represents an individual sporting fixture, capturing participating teams,
 * scheduled venue/time, live scorelines, bracket stages, and timeline incidents.
 */
class GameMatch extends Model
{
    use HasFactory;

    // ==========================================
    // Mass-Assignable Attributes
    // ==========================================
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

    // ==========================================
    // Attribute Type Casts
    // ==========================================
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'match_data' => 'array',
            'home_score' => 'integer',
            'away_score' => 'integer',
        ];
    }

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Associated tournament competition (null for friendly matches).
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Home team participant.
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * Away team participant.
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    /**
     * Player call-up invitations for this match fixture.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(MatchInvitation::class, 'match_id');
    }

    /**
     * News articles or disciplinary reports linked to this match.
     */
    public function newsArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class, 'match_id');
    }

    /**
     * Fan and spectator bookmarks following this fixture.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    /**
     * Check if the match is a standalone exhibition / friendly.
     */
    public function isFriendly(): bool
    {
        return is_null($this->tournament_id);
    }
}
