<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;

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

// Billing: monthly invoice run + daily dunning (idempotent per architecture/queue-jobs).
Schedule::command('flowflex:generate-monthly-invoices')
    ->monthlyOn(1, '01:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('flowflex:process-dunning')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->onOneServer();

// Audit log retention (architecture/data-lifecycle).
Schedule::command('flowflex:prune-audit-log')
    ->dailyAt('04:30')
    ->withoutOverlapping()
    ->onOneServer();

// Webhook delivery log retention.
Schedule::command('flowflex:prune-webhook-deliveries')
    ->dailyAt('04:45')
    ->withoutOverlapping()
    ->onOneServer();

// DSAR deadline reminders (-7d / -1d).
Schedule::command('flowflex:dsar-deadline-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();

// Health checks every minute (feeds /health + SystemStatusPage).
Schedule::command(RunHealthChecksCommand::class)
    ->everyMinute()
    ->withoutOverlapping();

// Queue heartbeat jobs — without these the QueueCheck always reports failed
// (it measures when a heartbeat job was last PROCESSED on each queue).
Schedule::command(DispatchQueueCheckJobsCommand::class)
    ->everyMinute()
    ->withoutOverlapping();
