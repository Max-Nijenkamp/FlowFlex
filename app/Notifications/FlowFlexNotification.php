<?php

namespace App\Notifications;

use App\Models\NotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class FlowFlexNotification extends Notification implements ShouldQueue
{
    use Queueable;

    abstract public function notificationType(): string;

    public function via(object $notifiable): array
    {
        $pref = NotificationPreference::where('tenant_id', $notifiable->id)
            ->where('notification_type', $this->notificationType())
            ->first();

        // Preference exists but is explicitly disabled — send nothing.
        if ($pref && ! $pref->is_enabled) {
            return [];
        }

        // Preference exists and is enabled — use its channels.
        if ($pref) {
            return $pref->channels ?? ['database'];
        }

        // No preference record — default to database only.
        return ['database'];
    }
}
