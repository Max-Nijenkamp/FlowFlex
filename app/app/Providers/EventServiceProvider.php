<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Foundation\CompanyCreated;
use App\Events\Foundation\UserActivated;
use App\Events\Foundation\UserInvited;
use App\Listeners\Foundation\LogUserActivatedListener;
use App\Listeners\Foundation\SendInviteMailListener;
use App\Listeners\Foundation\SyncOwnerPermissionsListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserInvited::class => [
            SendInviteMailListener::class,
        ],
        CompanyCreated::class => [
            SyncOwnerPermissionsListener::class,
        ],
        UserActivated::class => [
            LogUserActivatedListener::class,
        ],
    ];
}
