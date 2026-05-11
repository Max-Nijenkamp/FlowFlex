<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Core\BillingSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
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

        $ownerRole->syncPermissions(Permission::all());

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

        // Create active billing subscription for demo company
        BillingSubscription::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id],
            [
                'status'   => 'active',
                'currency' => 'EUR',
            ],
        );

        // Activate demo modules for the company
        $moduleKeys = [
            'hr.profiles',
            'hr.leave',
            'hr.onboarding',
            'hr.payroll',
            'hr.analytics',
            'projects.tasks',
            'projects.kanban',
            'projects.sprints',
            'projects.time',
            'projects.milestones',
        ];

        foreach ($moduleKeys as $moduleKey) {
            CompanyModuleSubscription::withoutGlobalScopes()->firstOrCreate(
                [
                    'company_id' => $company->id,
                    'module_key' => $moduleKey,
                ],
                [
                    'status'       => 'active',
                    'activated_at' => now(),
                ],
            );
        }
    }
}
