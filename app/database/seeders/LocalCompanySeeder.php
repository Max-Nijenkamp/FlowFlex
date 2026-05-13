<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class LocalCompanySeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $company = Company::firstOrCreate(
            ['slug' => 'flowflex-demo'],
            [
                'name' => 'FlowFlex Demo',
                'email' => 'demo@flowflex.io',
                'status' => 'active',
                'timezone' => 'Europe/Amsterdam',
                'locale' => 'en',
                'currency' => 'EUR',
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@test.nl', 'company_id' => $company->id],
            ['name' => 'Test User', 'password' => bcrypt('test1234'), 'status' => 'active']
        );
    }
}
