<?php

use App\Models\ApiKey;
use App\Models\Company;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Create a Company with sensible defaults.
 */
function makeCompany(array $attrs = []): Company
{
    return Company::create(array_merge([
        'name'       => 'Test Company ' . Str::random(6),
        'slug'       => 'test-co-' . Str::random(6),
        'email'      => 'admin@test-' . Str::random(4) . '.com',
        'is_enabled' => true,
        'timezone'   => 'UTC',
        'settings'   => [],
    ], $attrs));
}

/**
 * Create a Tenant (workspace user) belonging to the given company.
 */
function makeTenant(Company $company, array $attrs = []): Tenant
{
    return Tenant::create(array_merge([
        'company_id' => $company->id,
        'first_name' => 'Test',
        'last_name'  => 'User',
        'email'      => 'tenant-' . Str::random(8) . '@example.com',
        'password'   => bcrypt('password'),
        'is_enabled' => true,
    ], $attrs));
}

/**
 * Create a super-admin User (web guard).
 */
function makeUser(array $attrs = []): User
{
    return User::create(array_merge([
        'name'     => 'Admin User',
        'email'    => 'admin-' . Str::random(8) . '@example.com',
        'password' => bcrypt('password'),
    ], $attrs));
}

/**
 * Give a tenant a Spatie permission (using the "tenant" guard).
 */
function givePermission(Tenant $tenant, string $permission): void
{
    // Ensure the permission exists for the tenant guard
    $perm = Permission::firstOrCreate(
        ['name' => $permission, 'guard_name' => 'tenant']
    );

    $tenant->givePermissionTo($perm);
}

/**
 * Give a tenant multiple permissions.
 */
function givePermissions(Tenant $tenant, array $permissions): void
{
    foreach ($permissions as $permission) {
        givePermission($tenant, $permission);
    }
}

/**
 * Create a Module and attach it to a company as enabled.
 */
function attachModule(Company $company, string $key, string $panelId): Module
{
    $module = Module::firstOrCreate(
        ['key' => $key],
        [
            'name'         => ucfirst($key),
            'description'  => ucfirst($key) . ' module',
            'domain'       => $key,
            'panel_id'     => $panelId,
            'is_core'      => false,
            'is_available' => true,
            'sort_order'   => 10,
        ]
    );

    $company->modules()->syncWithoutDetaching([
        $module->id => [
            'is_enabled' => true,
            'enabled_at' => now(),
        ],
    ]);

    return $module;
}

/**
 * Create a plain API key for the given company, returning [key, model].
 *
 * @return array{key: string, model: ApiKey}
 */
function makeApiKey(Company $company, array $attrs = []): array
{
    ['key' => $plaintext, 'hash' => $hash, 'prefix' => $prefix] = ApiKey::generateKey();

    $model = ApiKey::create(array_merge([
        'company_id' => $company->id,
        'name'       => 'Test Key',
        'key_hash'   => $hash,
        'key_prefix' => $prefix,
        'scopes'     => [],
    ], $attrs));

    return ['key' => $plaintext, 'model' => $model];
}
