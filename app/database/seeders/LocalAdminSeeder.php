<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class LocalAdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'test@test.nl'],
            [
                'name'     => 'Test Admin',
                'email'    => 'test@test.nl',
                'password' => bcrypt('test1234'),
                'role'     => 'super_admin',
            ],
        );
    }
}
