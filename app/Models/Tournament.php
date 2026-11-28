<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Tournament Model
 *
 * Represents a competitive tournament, storing scheduling, format,
 * stage progression, prize pool, and participating teams.
 */
class Tournament extends Model
{
    use HasFactory;

    // ==========================================
    // Mass-Assignable Attributes
    // ==========================================
    protected $fillable = [
        'creator_id',
        'title',
        'slug',
        'sport_type',
        'status',
        'current_stage',
        'banner',
        'description',
        'venue',
        'start_date',
        'end_date',
        'prize_pool',
        'max_teams',
    ];

    // ==========================================
    // Attribute Type Casts
    // ==========================================
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'max_teams' => 'integer',
        ];
    }

    // ==========================================
    // Model Lifecycle Events
    // Automatically generate a slug if not explicitly supplied
    // ==========================================
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($tournament) {
            if (empty($tournament->slug)) {
                $tournament->slug = Str::slug($tournament->title).'-'.Str::random(5);
            }
        });
    }

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * The organizer user who created the competition.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * All team entry applications submitted for this tournament.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(TournamentApplication::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(TournamentInvitation::class);
    }

    /**
     * Approved participating teams confirmed for the competition bracket.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'tournament_applications')
            ->wherePivot('status', 'approved')
            ->withPivot('status', 'notes')
            ->withTimestamps();
    }

    /**
     * All teams that have submitted an application regardless of review state.
     */
    public function allAppliedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'tournament_applications')
            ->withPivot('status', 'notes')
            ->withTimestamps();
    }

    /**
     * Fixture matches belonging to this tournament ordered chronologically.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class)->orderBy('scheduled_at');
    }

    /**
     * User bookmarks following this tournament.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }
}
