<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Production seeders — always run, idempotent.
        $this->call([
            PermissionSeeder::class,
            ModuleCatalogSeeder::class,
        ]);

        // Local/demo data only outside production.
        if (! app()->environment('production')) {
            $this->call(LocalDevSeeder::class);
        }
    }
}
