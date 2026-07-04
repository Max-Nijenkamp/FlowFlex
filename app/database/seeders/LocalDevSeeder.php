<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use App\Support\Services\BuiltInRoles;
use App\Support\Services\CompanyContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Spatie\Permission\Models\Role;

/**
 * Non-production demo data: the "FlowFlex Demo" company + working logins.
 * Idempotent. Refuses to run in production.
 */
class LocalDevSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('LocalDevSeeder must never run in production.');
        }

        // --- FlowFlex staff admins -------------------------------------------------
        Admin::query()->firstOrCreate(
            ['email' => 'admin@flowflex.nl'],
            ['name' => 'FlowFlex Admin', 'password' => 'password', 'role' => 'super_admin'],
        );

        Admin::query()->firstOrCreate(
            ['email' => 'test@test.nl'],
            ['name' => 'Test Staff', 'password' => 'test1234', 'role' => 'super_admin'],
        );

        // --- Demo tenant -----------------------------------------------------------
        $company = Company::query()->firstOrCreate(
            ['slug' => 'flowflex-demo'],
            [
                'name' => 'FlowFlex Demo',
                'subscription_status' => 'active',
                'setup_completed_at' => now(),
                'trial_ends_at' => null,
            ],
        );

        app(CompanyContext::class)->set($company);
        setPermissionsTeamId($company->id);

        // Built-in roles (owner/admin/manager/employee) with default grants.
        BuiltInRoles::ensure($company);
        $owner = Role::query()->where(['name' => 'owner', 'company_id' => $company->id])->firstOrFail();

        $ownerLogins = [
            ['email' => 'test@test.nl', 'password' => 'test1234', 'first' => 'Test', 'last' => 'Owner'],
            ['email' => 'demo@flowflex.nl', 'password' => 'password', 'first' => 'Demo', 'last' => 'Owner'],
        ];

        foreach ($ownerLogins as $login) {
            $user = User::query()->withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'email' => $login['email']],
                [
                    'first_name' => $login['first'],
                    'last_name' => $login['last'],
                    'password' => $login['password'],
                    'email_verified_at' => now(),
                ],
            );

            $user->assignRole($owner);
        }

        // --- Extra demo users — employees (exactly-one-owner invariant) -------------
        User::factory()
            ->count(5)
            ->for($company)
            ->create()
            ->each(fn (User $user) => $user->assignRole('employee'));

        // Second demo login keeps admin, not owner — one owner per company.
        User::query()->withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('email', 'demo@flowflex.nl')
            ->first()
            ?->syncRoles(['admin']);

        // --- Free core modules active for the demo company --------------------------
        $demoOwner = User::query()->withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('email', 'test@test.nl')
            ->first();

        foreach (ModuleCatalogSeeder::CATALOG as $key => $entry) {
            if ($entry['price'] !== 0) {
                continue;
            }

            CompanyModuleSubscription::query()->firstOrCreate(
                ['company_id' => $company->id, 'module_key' => $key, 'deactivated_at' => null],
                ['activated_at' => now(), 'activated_by' => $demoOwner?->id],
            );
        }

        Cache::forget("company:{$company->id}:modules");
    }
}
