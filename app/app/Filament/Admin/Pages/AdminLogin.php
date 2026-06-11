<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use Filament\Auth\Pages\Login;

/** Staff console login — same Filament login, clearly labelled. */
class AdminLogin extends Login
{
    public function getHeading(): string
    {
        return 'Staff console';
    }

    public function getSubheading(): ?string
    {
        return 'FlowFlex staff only. Customers sign in from flowflex.eu.';
    }
}
