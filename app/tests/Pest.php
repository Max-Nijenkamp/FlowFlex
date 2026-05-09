<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        // Reset rate limiters between tests so Livewire auth tests don't hit limits
        RateLimiter::clear(
            'livewire-rate-limiter:' . sha1(\Filament\Auth\Pages\Login::class . '|authenticate|127.0.0.1')
        );
    })
    ->in('Feature');

pest()->extend(TestCase::class)
    ->in('Unit');
