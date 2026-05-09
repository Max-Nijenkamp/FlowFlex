<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class LocalCompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['slug' => 'flowflex-demo'],
            [
                'name'     => 'FlowFlex Demo',
                'slug'     => 'flowflex-demo',
                'email'    => 'demo@flowflex.local',
                'status'   => 'active',
                'timezone' => 'Europe/Amsterdam',
                'locale'   => 'en',
                'currency' => 'EUR',
            ],
        );

        setPermissionsTeamId($company->id);

        $ownerRole = Role::firstOrCreate(
            ['name' => 'owner', 'guard_name' => 'web', 'team_id' => $company->id],
        );

        $user = User::firstOrCreate(
            ['email' => 'test@test.nl'],
            [
                'company_id'        => $company->id,
                'first_name'        => 'Test',
                'last_name'         => 'User',
                'email'             => 'test@test.nl',
                'password'          => bcrypt('test1234'),
                'status'            => 'active',
                'locale'            => 'en',
                'timezone'          => 'Europe/Amsterdam',
                'email_verified_at' => now(),
            ],
        );

        $user->assignRole($ownerRole);
    }
}
