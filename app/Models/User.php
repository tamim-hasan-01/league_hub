<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'areal_zone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Role checks
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isPlayer(): bool
    {
        return $this->role === 'player';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    // Relationships
    public function playerProfile(): HasOne
    {
        return $this->hasOne(PlayerProfile::class);
    }

    public function managedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'manager_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user')
            ->withPivot('position', 'status')
            ->withTimestamps();
    }

    public function matchInvitations(): HasMany
    {
        return $this->hasMany(MatchInvitation::class, 'player_id');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function createdTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'creator_id');
    }

    public function authoredArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class, 'author_id');
    }
}
