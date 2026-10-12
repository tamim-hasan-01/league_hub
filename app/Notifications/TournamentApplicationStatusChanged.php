<?php

namespace App\Notifications;

use App\Models\TournamentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TournamentApplicationStatusChanged extends Notification
{
    use Queueable;

    public TournamentApplication $application;

    public function __construct(TournamentApplication $application)
    {
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_status',
            'title' => 'Tournament Application ' . ucfirst($this->application->status),
            'message' => 'Your team ' . ($this->application->team->name ?? '') . ' registration for "' . ($this->application->tournament->title ?? '') . '" has been ' . $this->application->status . '.',
            'tournament_id' => $this->application->tournament_id,
            'action_url' => route('mytournaments'),
        ];
    }
}

