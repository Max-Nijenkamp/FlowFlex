<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as BaseAuthenticate;

class AuthenticateTenant extends BaseAuthenticate
{
    protected function redirectTo($request): string
    {
        return route('filament.workspace.auth.login');
    }
}
