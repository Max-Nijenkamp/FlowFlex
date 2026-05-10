<?php

declare(strict_types=1);

namespace App\Contracts\Core;

use App\Models\User;
use Illuminate\Notifications\Notification;

interface NotifiableEvent
{
    public function eventType(): string;

    public function priority(): string; // critical | high | normal | low

    public function toNotification(User $user): Notification;
}
