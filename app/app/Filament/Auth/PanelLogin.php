<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login;

/**
 * Shared login page for both panels. Filament's built-in login rate limiting
 * applies; the guard split (web vs admin) is the authorization boundary.
 */
class PanelLogin extends Login {}
