<?php

declare(strict_types=1);

namespace App\Support\Notifications;

use App\Events\NotificationCreated;
use App\Models\User;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Base class for every FlowFlex notification (core.notifications):
 * preference-resolved channels, tenant-stamped database payload, and a
 * Reverb broadcast on the owning company's channel after commit.
 */
abstract class FlowFlexNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** The registry key in NotificationPreferenceService::TYPES. */
    abstract public function notificationType(): string;

    abstract public function title(): string;

    abstract public function body(): string;

    public function actionUrl(): ?string
    {
        return null;
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        if (! $notifiable instanceof User) {
            return ['database'];
        }

        return app(NotificationPreferenceService::class)
            ->channelsFor($notifiable, $this->notificationType());
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        if ($notifiable instanceof User) {
            NotificationCreated::dispatch($notifiable->company_id, $notifiable->id);
        }

        // Filament's bell reads this shape (format => filament).
        return [
            'title' => $this->title(),
            'body' => $this->body(),
            'actions' => [],
            'action_url' => $this->actionUrl(),
            'format' => 'filament',
            'domain' => str($this->notificationType())->before('-')->toString(),
            'company_id' => $notifiable instanceof User ? $notifiable->company_id : null,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title())
            ->line($this->body());

        if ($this->actionUrl() !== null) {
            $mail->action('Open FlowFlex', $this->actionUrl());
        }

        return $mail;
    }
}
