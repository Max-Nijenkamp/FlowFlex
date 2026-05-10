<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Contracts\Core\NotifiableEvent;
use App\Models\Core\NotificationLog;
use App\Models\Core\NotificationPreference;
use App\Models\Core\NotificationQuietHours;
use App\Models\User;
use App\Support\Services\CompanyContext;

class NotificationRouter
{
    public function __construct(private readonly CompanyContext $companyContext) {}

    public function route(NotifiableEvent $event, User $user): void
    {
        if ($event->priority() === 'critical') {
            $this->dispatch($event, $user, ['database', 'mail']);

            return;
        }

        $channels = $this->enabledChannelsFor($user, $event->eventType());

        if (empty($channels)) {
            return;
        }

        if ($event->priority() !== 'critical' && $this->isQuietHours($user)) {
            // Still create in-app notification; suppress push/mail during quiet hours
            $channels = array_filter($channels, fn ($c) => $c === 'database');
        }

        $this->dispatch($event, $user, $channels);
    }

    private function enabledChannelsFor(User $user, string $eventType): array
    {
        $prefs = NotificationPreference::where('user_id', $user->id)
            ->where('event_type', $eventType)
            ->where('enabled', true)
            ->pluck('channel')
            ->toArray();

        return ! empty($prefs) ? $prefs : ['database'];
    }

    private function isQuietHours(User $user): bool
    {
        $quietHours = NotificationQuietHours::where('user_id', $user->id)->first();

        return $quietHours && $quietHours->isActive();
    }

    private function dispatch(NotifiableEvent $event, User $user, array $channels): void
    {
        $notification = $event->toNotification($user);

        $user->notify($notification);

        $company = $this->companyContext->hasCompany() ? $this->companyContext->current() : null;

        foreach ($channels as $channel) {
            NotificationLog::create([
                'company_id' => $company?->id,
                'user_id'    => $user->id,
                'event_type' => $event->eventType(),
                'channel'    => $channel,
                'status'     => 'sent',
                'sent_at'    => now(),
            ]);
        }
    }
}
