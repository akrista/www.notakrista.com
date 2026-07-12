<?php

declare(strict_types=1);

namespace App\Notifications\Teams;

use App\Models\TeamInvitation as TeamInvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class TeamInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TeamInvitationModel $invitation)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $team = $this->invitation->team;
        $inviter = $this->invitation->inviter;

        $teamName = $team !== null ? $team->name : '';
        $inviterName = $inviter !== null ? $inviter->name : '';

        return (new MailMessage)
            ->subject(__('app.notification_invitation_subject', ['teamName' => $teamName]))
            ->line(__('app.notification_invitation_line1', [
                'inviterName' => $inviterName,
                'teamName' => $teamName,
            ]))
            ->line(__('app.notification_invitation_line2'))
            ->action(
                __('app.log_in'),
                route('login', ['invitation' => $this->invitation->code]),
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $team = $this->invitation->team;
        $teamName = $team !== null ? $team->name : '';

        return [
            'invitation_id' => $this->invitation->id,
            'team_id' => $this->invitation->team_id,
            'team_name' => $teamName,
            'role' => $this->invitation->role->value,
        ];
    }
}
