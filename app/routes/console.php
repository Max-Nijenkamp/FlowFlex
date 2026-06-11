<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Prune failed jobs older than 30 days. Idempotency provided by the framework.
Schedule::command('queue:prune-failed --hours=720')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer();

// Prune Horizon metrics/snapshots.
Schedule::command('horizon:snapshot')->everyFiveMinutes()->withoutOverlapping();
