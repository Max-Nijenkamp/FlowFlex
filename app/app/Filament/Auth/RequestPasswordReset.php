<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Contracts\Support\Htmlable;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function getHeading(): string|Htmlable
    {
        return 'Reset your password';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Type the work email you sign in with. If it has a FlowFlex account, a reset link is on its way.';
    }
}
