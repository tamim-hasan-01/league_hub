<?php

namespace App\Notifications;

use App\Models\GameMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MatchResultNotification extends Notification
{
    use Queueable;

    public GameMatch $match;

    public function __construct(GameMatch $match)
    {
        $this->match = $match;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'match_result',
            'title' => 'Final Result: ' . $this->match->homeTeam->name . ' vs ' . $this->match->awayTeam->name,
            'message' => 'Final score: ' . $this->match->home_score . ' - ' . $this->match->away_score . ' (' . ucfirst($this->match->stage) . ').',
            'match_id' => $this->match->id,
            'action_url' => route('home'),
        ];
    }
}

