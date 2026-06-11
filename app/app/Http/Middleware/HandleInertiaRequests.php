<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /** @return array<string, mixed> */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() !== null ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->full_name,
                    'email' => $request->user()->email,
                ] : null,
                'company' => $request->user() !== null ? [
                    'id' => $request->user()->company_id,
                    'name' => $request->user()->company->name,
                ] : null,
                'permissions' => $request->user() !== null
                    ? $request->user()->getAllPermissions()->pluck('name')
                    : [],
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ]);
    }
}
