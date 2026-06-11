<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class MarketplaceModuleData extends Data
{
    public function __construct(
        public readonly string $module_key,
        public readonly string $name,
        public readonly string $domain,
        public readonly int $per_user_monthly_price_cents,
        public readonly int $price_preview_cents,
        public readonly bool $is_active_for_company,
        public readonly bool $is_free_core,
    ) {}
}
