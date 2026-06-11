<?php

declare(strict_types=1);

namespace App\Support\Notifications;

use App\Models\User;
use App\Services\NotificationPreferenceService;
use App\Support\Services\CompanyContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Base for all FlowFlex notifications:
 * - queued on the `notifications` queue
 * - channels resolved from per-user preferences (database/mail)
 * - database payload always carries company_id + title/body/action_url/domain
 *
 * Reverb broadcast on company.{id}.notifications lands with the websockets pass.
 */
abstract class FlowFlexNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue('notifications');
    }

    abstract public function title(): string;

    abstract public function body(): string;

    public function actionUrl(): ?string
    {
        return null;
    }

    public function domain(): string
    {
        return explode('.', static::typeKey())[0];
    }

    /** Preference key — defaults to the class name. */
    public static function typeKey(): string
    {
        return static::class;
    }

    /** @return list<string> */
    public function via(User $notifiable): array
    {
        return app(NotificationPreferenceService::class)->channelsFor($notifiable, static::typeKey());
    }

    /** @return array<string, mixed> */
    public function toDatabase(User $notifiable): array
    {
        return [
            'title' => $this->title(),
            'body' => $this->body(),
            'action_url' => $this->actionUrl(),
            'domain' => $this->domain(),
            'company_id' => app(CompanyContext::class)->currentId() ?? $notifiable->company_id,
        ];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title())
            ->line($this->body());

        if ($this->actionUrl() !== null) {
            $mail->action('View', $this->actionUrl());
        }

        return $mail;
    }
}
