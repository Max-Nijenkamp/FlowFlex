<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

/**
 * Shared panel login. Moves the "forgot password" link from the label hint
 * to below the password input — public-login parity, and tabbing goes
 * email → password without passing the link.
 */
class PanelLogin extends Login
{
    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->hint(null)
            ->belowContent(filament()->hasPasswordReset() ? new HtmlString(Blade::render(
                '<div class="ff-login-forgot"><x-filament::link :href="filament()->getRequestPasswordResetUrl()">{{ __(\'filament-panels::auth/pages/login.actions.request_password_reset.label\') }}</x-filament::link></div>'
            )) : null);
    }
}
