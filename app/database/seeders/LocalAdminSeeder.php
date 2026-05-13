<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class LocalAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        Admin::firstOrCreate(
            ['email' => 'test@test.nl'],
            ['name' => 'Test Admin', 'password' => bcrypt('test1234'), 'role' => 'super_admin']
        );
    }
}
