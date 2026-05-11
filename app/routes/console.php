<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Prune activity logs older than 90 days
Schedule::command('activitylog:clean --days=90')->daily();

// Delete expired invitations
Schedule::call(function () {
    \App\Models\UserInvitation::where('expires_at', '<', now())->delete();
})->daily()->name('clean-expired-invitations');
