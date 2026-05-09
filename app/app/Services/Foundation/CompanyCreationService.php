<?php

declare(strict_types=1);

namespace App\Services\Foundation;

use App\Data\Foundation\CreateCompanyData;
use App\Events\Foundation\CompanyCreated;
use App\Events\Foundation\UserInvited;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalog;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CompanyCreationService
{
    /**
     * Core modules always activated for every new company.
     */
    private const FOUNDATION_MODULES = [
        'core.auth',
        'core.notifications',
        'core.audit-log',
        'core.file-storage',
        'core.rbac',
    ];

    public function create(CreateCompanyData $data): Company
    {
        return DB::transaction(function () use ($data): Company {
            // 1. Create the company
            $company = Company::withoutGlobalScopes()->create([
                'name'          => $data->name,
                'slug'          => $data->slug,
                'email'         => $data->email,
                'country'       => $data->country,
                'status'        => 'trial',
                'timezone'      => $data->timezone,
                'locale'        => $data->locale,
                'currency'      => $data->currency,
                'trial_ends_at' => now()->addDays(14),
            ]);

            // 2. Create the owner user (no CompanyScope active yet)
            $owner = User::withoutGlobalScopes()->create([
                'company_id' => $company->id,
                'first_name' => $data->owner_first_name,
                'last_name'  => $data->owner_last_name,
                'email'      => $data->owner_email,
                'status'     => 'invited',
                'timezone'   => $data->timezone,
                'locale'     => $data->locale,
            ]);

            // 3. Set permissions team context to this company
            setPermissionsTeamId($company->id);

            // 4. Create the owner role scoped to this company
            $ownerRole = Role::create([
                'name'       => 'owner',
                'guard_name' => 'web',
            ]);

            // 5. Sync all permissions to owner role
            $allPermissions = Permission::all();
            $ownerRole->syncPermissions($allPermissions);

            // 6. Assign owner role to user
            $owner->assignRole($ownerRole);

            // 7. Activate foundation modules (always free)
            $this->activateFoundationModules($company);

            // 8. Activate any additional starter modules selected by admin
            if ($data->starter_modules) {
                $this->activateStarterModules($company, $data->starter_modules);
            }

            // 9. Generate invite token and persist to DB
            $inviteToken = Str::random(64);

            UserInvitation::create([
                'user_id'    => $owner->id,
                'company_id' => $company->id,
                'token'      => $inviteToken,
                'expires_at' => now()->addDays(7),
            ]);

            // 10. Fire events
            event(new CompanyCreated($company, $owner));
            event(new UserInvited($owner, $company, $inviteToken));

            return $company;
        });
    }

    private function activateFoundationModules(Company $company): void
    {
        foreach (self::FOUNDATION_MODULES as $moduleKey) {
            CompanyModuleSubscription::withoutGlobalScopes()->create([
                'company_id'   => $company->id,
                'module_key'   => $moduleKey,
                'status'       => 'active',
                'activated_at' => now(),
            ]);
        }
    }

    private function activateStarterModules(Company $company, array $moduleKeys): void
    {
        foreach ($moduleKeys as $moduleKey) {
            if (in_array($moduleKey, self::FOUNDATION_MODULES, true)) {
                continue; // Already activated above
            }

            $exists = ModuleCatalog::where('module_key', $moduleKey)
                ->where('is_active', true)
                ->exists();

            if ($exists) {
                CompanyModuleSubscription::withoutGlobalScopes()->create([
                    'company_id'   => $company->id,
                    'module_key'   => $moduleKey,
                    'status'       => 'active',
                    'activated_at' => now(),
                ]);
            }
        }
    }
}
