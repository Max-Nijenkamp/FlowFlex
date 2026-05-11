<?php

declare(strict_types=1);

namespace App\Notifications\Foundation;

use App\Models\PlatformAnnouncement;
use App\Notifications\Concerns\HasResolvedChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlatformAnnouncementNotification extends Notification
{
    use HasResolvedChannels;
    use Queueable;

    public function __construct(private readonly PlatformAnnouncement $announcement) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title'           => $this->announcement->title,
            'body'            => $this->announcement->body,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->announcement->title)
            ->markdown('emails.announcements.platform', [
                'announcement' => $this->announcement,
                'user'         => $notifiable,
            ]);
    }
}
