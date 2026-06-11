<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the module catalog (prices, activation). The `module_catalog` table
 * arrives with core.billing — until then this is a guarded no-op so
 * `migrate --seed` stays clean at the Foundation stage.
 */
class ModuleCatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('module_catalog')) {
            $this->command?->info('ModuleCatalogSeeder skipped — module_catalog table not yet created (arrives with core.billing).');

            return;
        }

        // Populated once core.billing defines the catalog rows.
    }
}
