<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(ModuleCatalogSeeder::class);

        if (! app()->environment('production')) {
            $this->call(LocalDevSeeder::class);
        }
    }
}
