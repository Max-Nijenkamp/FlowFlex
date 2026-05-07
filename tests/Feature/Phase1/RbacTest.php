<?php

use App\Models\Company;
use App\Models\Tenant;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);
});

it('tenant without permission cannot access HR departments list', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/departments')
        ->assertForbidden();
});

it('tenant with hr.departments.view permission can access HR departments list', function () {
    givePermission($this->tenant, 'hr.departments.view');
    attachModule($this->company, 'hr', 'hr');

    // NOTE: 500 is an app-level bug (Tenant missing getFilamentName()).
    // The canViewAny() gate check passes; the 500 happens when rendering the user menu.
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/departments')
        ->assertStatus(200);
});

it('canViewAny returns false when tenant lacks permission', function () {
    $this->actingAs($this->tenant, 'tenant');

    expect(\App\Filament\Hr\Resources\DepartmentResource::canViewAny())->toBeFalse();
});

it('canViewAny returns true when tenant has permission', function () {
    givePermission($this->tenant, 'hr.departments.view');
    $this->actingAs($this->tenant, 'tenant');

    expect(\App\Filament\Hr\Resources\DepartmentResource::canViewAny())->toBeTrue();
});

it('canCreate returns false without create permission', function () {
    $this->actingAs($this->tenant, 'tenant');

    expect(\App\Filament\Hr\Resources\DepartmentResource::canCreate())->toBeFalse();
});

it('canCreate returns true with create permission', function () {
    givePermission($this->tenant, 'hr.departments.create');
    $this->actingAs($this->tenant, 'tenant');

    expect(\App\Filament\Hr\Resources\DepartmentResource::canCreate())->toBeTrue();
});

it('canEdit returns false without edit permission', function () {
    $this->actingAs($this->tenant, 'tenant');
    $record = new \App\Models\Hr\Department();

    expect(\App\Filament\Hr\Resources\DepartmentResource::canEdit($record))->toBeFalse();
});

it('canDelete returns false without delete permission', function () {
    $this->actingAs($this->tenant, 'tenant');
    $record = new \App\Models\Hr\Department();

    expect(\App\Filament\Hr\Resources\DepartmentResource::canDelete($record))->toBeFalse();
});

it('permissions are scoped to tenant guard not web guard', function () {
    $admin = makeUser();
    $this->actingAs($admin, 'web');

    // Admin uses web guard — canViewAny for Hr resource (which uses auth()->user() = web)
    // should return false since admin doesn't have the tenant permission
    expect(\App\Filament\Hr\Resources\DepartmentResource::canViewAny())->toBeFalse();
});

it('role can be assigned to tenant and grants permissions', function () {
    $role = Role::firstOrCreate(['name' => 'hr_manager', 'guard_name' => 'tenant']);
    $perm = Permission::firstOrCreate(['name' => 'hr.departments.view', 'guard_name' => 'tenant']);
    $role->givePermissionTo($perm);

    $this->tenant->assignRole($role);

    $this->actingAs($this->tenant, 'tenant');

    expect(\App\Filament\Hr\Resources\DepartmentResource::canViewAny())->toBeTrue();
});

it('permission check on employee resource works correctly', function () {
    givePermission($this->tenant, 'hr.employees.view');
    $this->actingAs($this->tenant, 'tenant');

    expect(\App\Filament\Hr\Resources\EmployeeResource::canViewAny())->toBeTrue();
});

it('WorkspaceSettings permission guard triggers 403 without permission', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/workspace/manage-company')
        ->assertForbidden();
});

it('WorkspaceSettings permission guard allows access with correct permission', function () {
    givePermission($this->tenant, 'workspace.settings.view');

    $this->actingAs($this->tenant, 'tenant')
        ->get('/workspace/manage-company')
        ->assertOk();
});
