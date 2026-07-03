<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the module catalog other domains gate on. The catalog tables
 * (module_catalog, company_module_subscriptions) are owned by
 * core.billing-engine — until that module ships its migrations this seeder
 * no-ops with a note, so `migrate --seed` stays clean at every phase.
 */
class ModuleCatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('module_catalog')) {
            $this->command?->warn(
                'ModuleCatalogSeeder skipped — module_catalog table lands with core.billing-engine.'
            );

            return;
        }

        // core.billing-engine will populate the catalog rows here when it ships.
    }
}
