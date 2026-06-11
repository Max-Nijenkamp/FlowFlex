<?php

declare(strict_types=1);

namespace App\Data\Core;

use Spatie\LaravelData\Data;

class ActivateModuleData extends Data
{
    public function __construct(
        public readonly string $module_key,
    ) {}

    /** @return array<string, array<int, string>> */
    public static function rules(): array
    {
        // Catalog existence + is_active + not-already-active validated in BillingService.
        return [
            'module_key' => ['required', 'string'],
        ];
    }
}
