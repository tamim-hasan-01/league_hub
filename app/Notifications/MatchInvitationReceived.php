<?php

namespace App\Notifications;

use App\Models\MatchInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MatchInvitationReceived extends Notification
{
    use Queueable;

    public MatchInvitation $invitation;

    public function __construct(MatchInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'match_invitation',
            'title' => 'New Match Contract & Callup',
            'message' => 'You have been summoned by ' . ($this->invitation->team->name ?? 'your team') . ' for the fixture on ' . ($this->invitation->match->scheduled_at->format('M d, H:i') ?? 'TBD') . ' at ' . ($this->invitation->match->venue ?? 'Arena') . '.',
            'match_id' => $this->invitation->match_id,
            'invitation_id' => $this->invitation->id,
            'action_url' => route('mytournaments'),
        ];
    }
}

