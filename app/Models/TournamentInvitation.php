<?php

namespace App\Models;

use Database\Factories\TournamentInvitationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentInvitation extends Model
{
    /** @use HasFactory<TournamentInvitationFactory> */
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'manager_id',
        'team_id',
        'target_type',
        'target_id',
        'invited_by',
        'status',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
