<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tournament extends Model
{
    use HasFactory;

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

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'max_teams' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($tournament) {
            if (empty($tournament->slug)) {
                $tournament->slug = Str::slug($tournament->title) . '-' . Str::random(5);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(TournamentApplication::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'tournament_applications')
            ->wherePivot('status', 'approved')
            ->withPivot('status', 'notes')
            ->withTimestamps();
    }

    public function allAppliedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'tournament_applications')
            ->withPivot('status', 'notes')
            ->withTimestamps();
    }

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class)->orderBy('scheduled_at');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }
}

