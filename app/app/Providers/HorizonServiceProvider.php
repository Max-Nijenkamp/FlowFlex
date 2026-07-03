<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // /horizon authenticates against the admin guard, never tenant users.
        Horizon::auth(fn ($request) => Gate::forUser($request->user('admin'))->check('viewHorizon'));
    }

    protected function gate(): void
    {
        Gate::define('viewHorizon', fn (mixed $user = null): bool => $user instanceof Admin);
    }
}
