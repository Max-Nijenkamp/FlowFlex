<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkspaceInviteNotification extends Notification
{
    public function __construct(
        private readonly string $plainPassword,
        private readonly string $workspaceName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("You've been invited to {$this->workspaceName}")
            ->line("You have been added to {$this->workspaceName} on FlowFlex.")
            ->line("Email: {$notifiable->email}")
            ->line("Temporary password: {$this->plainPassword}")
            ->line('Please change your password after your first login.')
            ->action('Login to FlowFlex', url('/'));
    }
}
