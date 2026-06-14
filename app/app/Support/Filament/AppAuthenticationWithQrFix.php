<?php

declare(strict_types=1);

namespace App\Support\Filament;

use Filament\Auth\MultiFactor\App\AppAuthentication;
use SensitiveParameter;

/**
 * Fixes the empty 2FA QR code: google2fa's getQRCodeInline() already returns
 * a complete `data:image/svg+xml;base64,…` URI with the bacon SVG backend,
 * but Filament's imagick-less fallback wraps that URI in base64 a second
 * time — the browser then renders an empty image. Detect the double wrap
 * and unwrap once.
 */
class AppAuthenticationWithQrFix extends AppAuthentication
{
    public function generateQrCodeDataUri(#[SensitiveParameter] string $secret): string
    {
        $uri = parent::generateQrCodeDataUri($secret);

        if (str_starts_with($uri, 'data:image/svg+xml;base64,')) {
            $decoded = base64_decode(substr($uri, strlen('data:image/svg+xml;base64,')), true);

            if ($decoded !== false && str_starts_with($decoded, 'data:image/')) {
                return $decoded;
            }
        }

        return $uri;
    }
}
