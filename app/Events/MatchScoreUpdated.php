<?php

namespace App\Events;

use App\Models\GameMatch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchScoreUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GameMatch $match;
    public string $eventType;
    public string $message;

    /**
     * Create a new event instance.
     */
    public function __construct(GameMatch $match, string $eventType = 'score_update', string $message = 'Score updated')
    {
        $this->match = $match->load(['homeTeam', 'awayTeam', 'tournament']);
        $this->eventType = $eventType;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('matches'),
            new Channel('match.' . $this->match->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'match.score.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'match_id' => $this->match->id,
            'home_team' => $this->match->homeTeam->name,
            'home_score' => $this->match->home_score,
            'away_team' => $this->match->awayTeam->name,
            'away_score' => $this->match->away_score,
            'status' => $this->match->status,
            'event_type' => $this->eventType,
            'message' => $this->message,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}

