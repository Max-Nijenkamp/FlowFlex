<?php

declare(strict_types=1);

use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        // Seed permissions so Filament pages can call hasPermissionTo() without errors
        $this->seed(PermissionSeeder::class);

        // Reset rate limiters between tests so Livewire auth tests don't hit limits
        RateLimiter::clear(
            'livewire-rate-limiter:' . sha1(\Filament\Auth\Pages\Login::class . '|authenticate|127.0.0.1')
        );
    })
    ->in('Feature');

pest()->extend(TestCase::class)
    ->in('Unit');
