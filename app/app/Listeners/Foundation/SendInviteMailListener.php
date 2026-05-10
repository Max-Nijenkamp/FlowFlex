<?php

declare(strict_types=1);

namespace App\Listeners\Foundation;

use App\Events\Foundation\UserInvited;
use App\Mail\Foundation\UserInvitedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendInviteMailListener implements ShouldQueue
{
    public int $tries = 3;
    public array $backoff = [10, 60, 300];
    public int $timeout = 30;

    public function handle(UserInvited $event): void
    {
        Mail::to($event->user->email)->send(
            new UserInvitedMail($event->user, $event->company, $event->inviteToken),
        );
    }
}
