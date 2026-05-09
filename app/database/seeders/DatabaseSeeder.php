<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleCatalogSeeder::class,
        ]);

        Admin::firstOrCreate(
            ['email' => 'max@flowflex.io'],
            [
                'name'     => 'Max Nijenkamp',
                'email'    => 'max@flowflex.io',
                'password' => bcrypt('changeme-on-first-login'),
                'role'     => 'super_admin',
            ],
        );

        if (app()->environment('local')) {
            $this->call([
                LocalAdminSeeder::class,
                LocalCompanySeeder::class,
            ]);
        }
    }
}
