<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Filament\Auth\PanelLogin;

/** Staff console login — same Filament login, clearly labelled. */
class AdminLogin extends PanelLogin
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
