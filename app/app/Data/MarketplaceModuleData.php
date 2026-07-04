<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class MarketplaceModuleData extends Data
{
    public function __construct(
        public string $module_key,
        public string $domain,
        public string $name,
        public int $per_user_monthly_price,
        public string $price_preview,
        public bool $is_free,
        public bool $is_subscribed,
    ) {}
}
