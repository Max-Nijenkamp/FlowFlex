<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // Only FlowFlex staff (admin guard) — never tenant users — may view Horizon.
        Horizon::auth(fn (Request $request): bool => app()->environment('local')
            || Auth::guard('admin')->check());
    }

    protected function gate(): void
    {
        // Authorization handled by Horizon::auth() above (admin guard).
    }
}
