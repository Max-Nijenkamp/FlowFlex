<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Shared login page, per-panel copy (design handoff §8–11). Filament's
 * built-in login rate limiting applies; the guard split is the boundary.
 */
class PanelLogin extends Login
{
    public function getHeading(): string|Htmlable
    {
        return $this->isStaff() ? 'Staff sign in' : 'Sign in to FlowFlex';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->isStaff()
            ? 'FlowFlex employees only. All sessions are audited.'
            : 'Welcome back.';
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->label($this->isStaff() ? 'Staff email' : 'Work email');
    }

    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()
            ->label('Keep me signed in')
            ->default(true);
    }

    private function isStaff(): bool
    {
        return filament()->getId() === 'admin';
    }
}
