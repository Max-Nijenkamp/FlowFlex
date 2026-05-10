<?php

declare(strict_types=1);

namespace App\Listeners\Foundation;

use App\Events\Foundation\UserActivated;

class LogUserActivatedListener
{
    public function handle(UserActivated $event): void
    {
        activity('audit')
            ->causedBy($event->user)
            ->withProperties(['event' => 'user_activated'])
            ->log('User account activated');
    }
}
