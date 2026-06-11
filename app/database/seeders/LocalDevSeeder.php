<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Local/demo data: FlowFlex staff admin, the "FlowFlex Demo" tenant + owner,
 * and a handful of demo users. Refuses to run in production.
 */
class LocalDevSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('LocalDevSeeder must never run in production.');
        }

        // FlowFlex staff console login.
        Admin::firstOrCreate(
            ['email' => 'admin@flowflex.nl'],
            ['name' => 'FlowFlex Admin', 'password' => Hash::make('password'), 'role' => 'super_admin'],
        );

        // Demo tenant.
        $company = Company::firstOrCreate(
            ['slug' => 'flowflex-demo'],
            ['name' => 'FlowFlex Demo', 'subscription_status' => 'active', 'setup_completed_at' => now()],
        );

        app(CompanyContext::class)->set($company);
        setPermissionsTeamId($company->id);

        // Owner role gets every permission; stays in sync as new permissions are seeded.
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web', 'team_id' => $company->id]);
        $owner->syncPermissions(Permission::where('guard_name', 'web')->get());

        $ownerUser = User::firstOrCreate(
            ['company_id' => $company->id, 'email' => 'demo@flowflex.nl'],
            ['first_name' => 'Demo', 'last_name' => 'Owner', 'password' => Hash::make('password'), 'email_verified_at' => now()],
        );
        $ownerUser->assignRole($owner);

        // Free core modules active for the demo company.
        app(\App\Contracts\Core\BillingServiceInterface::class)->seedFreeCoreModules($company->id);

        // A few extra demo users.
        User::factory()->forCompany($company)->count(5)->create();
    }
}
