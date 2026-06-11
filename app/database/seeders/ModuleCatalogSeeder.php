<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * The module catalog is code-defined (ModuleCatalog Sushi model: free core set
 * + config('flowflex.modules') for paid domain modules) — nothing to seed.
 * Kept so deploy scripts referencing it stay valid.
 */
class ModuleCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Module catalog is code-defined (ModuleCatalog + flowflex.modules config) — nothing to seed.');
    }
}
