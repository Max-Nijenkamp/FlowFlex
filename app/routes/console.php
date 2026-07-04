<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Every scheduled command declares withoutOverlapping + onOneServer
// (foundation.queue-workers/scheduled-commands — single-instance rule).
Schedule::command('horizon:snapshot')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('audit:prune')
    ->dailyAt('04:30')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('billing:generate-invoices')
    ->monthlyOn(1, '01:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('billing:process-dunning')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->onOneServer();
