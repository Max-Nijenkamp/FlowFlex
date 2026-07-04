<?php

declare(strict_types=1);

namespace App\Support\Filament;

use Filament\Auth\MultiFactor\App\AppAuthentication;
use SensitiveParameter;

/**
 * core.two-factor-auth/qr-code-fix: on imagick-less hosts Filament wraps
 * google2fa's inline QR in a base64 data URI. When google2fa itself already
 * returned a complete data URI (bacon SVG backend, version-dependent), that
 * wrap double-encodes it and the enrollment QR renders as an empty image.
 * This override unwraps exactly one layer when — and only when — the
 * double-wrap is detected, so it is safe on either google2fa behavior.
 */
class AppAuthenticationWithQrFix extends AppAuthentication
{
    private const SVG_DATA_URI_PREFIX = 'data:image/svg+xml;base64,';

    public function generateQrCodeDataUri(#[SensitiveParameter] string $secret): string
    {
        return self::unwrapDoubleEncodedDataUri(parent::generateQrCodeDataUri($secret));
    }

    public static function unwrapDoubleEncodedDataUri(string $uri): string
    {
        if (! str_starts_with($uri, self::SVG_DATA_URI_PREFIX)) {
            return $uri;
        }

        $decoded = base64_decode(substr($uri, strlen(self::SVG_DATA_URI_PREFIX)), true);

        if ($decoded !== false && str_starts_with($decoded, self::SVG_DATA_URI_PREFIX)) {
            return $decoded; // the payload was itself a complete data URI — one layer too many
        }

        return $uri;
    }
}
