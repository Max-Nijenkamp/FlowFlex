<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

/** Bell badge update on company.{id}.notifications (event-bus + websockets). */
class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public readonly string $company_id,
        public readonly string $user_id,
        public readonly string $title,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->company_id}.notifications");
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }
}
