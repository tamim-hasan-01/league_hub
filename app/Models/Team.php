<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Team Model
 *
 * Represents an athletic club or esports roster, including managers,
 * active player squads, tournament registrations, home venue, and historical records.
 */
class Team extends Model
{
    use HasFactory;

    // ==========================================
    // Mass-Assignable Attributes
    // ==========================================
    protected $fillable = [
        'manager_id',
        'name',
        'slug',
        'logo',
        'sport_type',
        'areal_ranking',
        'description',
        'home_venue',
    ];

    // ==========================================
    // Attribute Type Casts
    // ==========================================
    protected function casts(): array
    {
        return [
            'areal_ranking' => 'integer',
        ];
    }

    // ==========================================
    // Model Lifecycle Events
    // Generate unique slug for team profile URLs
    // ==========================================
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = Str::slug($team->name).'-'.Str::random(5);
            }
        });
    }

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * The manager user account administering this team.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Active squad players signed to the team roster.
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('position', 'status')
            ->withTimestamps();
    }

    /**
     * Matches played where this team was designated as home team.
     */
    public function homeMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'home_team_id');
    }

    /**
     * Matches played where this team was designated as away team.
     */
    public function awayMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'away_team_id');
    }

    /**
     * Query builder for all matches involving this team (home or away).
     */
    public function matches()
    {
        return GameMatch::where(function ($query) {
            $query->where('home_team_id', $this->id)
                ->orWhere('away_team_id', $this->id);
        });
    }

    /**
     * Applications submitted by this team to join tournaments.
     */
    public function tournamentApplications(): HasMany
    {
        return $this->hasMany(TournamentApplication::class);
    }

    public function tournamentInvitations(): HasMany
    {
        return $this->hasMany(TournamentInvitation::class);
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(TeamJoinRequest::class);
    }

    /**
     * Tournaments in which this team is registered or participated.
     */
    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class, 'tournament_applications')
            ->withPivot('status', 'notes')
            ->withTimestamps();
    }

    // ==========================================
    // Calculated Attributes
    // ==========================================

    /**
     * Compute cumulative win/loss/draw record from completed fixtures.
     */
    public function getRecordAttribute(): array
    {
        $matches = GameMatch::where('status', 'completed')
            ->where(function ($q) {
                $q->where('home_team_id', $this->id)
                    ->orWhere('away_team_id', $this->id);
            })->get();

        $wins = 0;
        $losses = 0;
        $draws = 0;

        foreach ($matches as $match) {
            if ($match->home_team_id == $this->id) {
                if ($match->home_score > $match->away_score) {
                    $wins++;
                } elseif ($match->home_score < $match->away_score) {
                    $losses++;
                } else {
                    $draws++;
                }
            } else {
                if ($match->away_score > $match->home_score) {
                    $wins++;
                } elseif ($match->away_score < $match->home_score) {
                    $losses++;
                } else {
                    $draws++;
                }
            }
        }

        return ['wins' => $wins, 'losses' => $losses, 'draws' => $draws, 'total' => $matches->count()];
    }
}
