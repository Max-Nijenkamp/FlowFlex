<?php

use App\Models\Company;
use App\Models\Hr\Department;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.departments.view',
        'hr.departments.create',
        'hr.departments.edit',
        'hr.departments.delete',
    ]);

    $this->dept = Department::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Engineering',
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list departments', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/departments')
        ->assertOk();
});

it('unauthenticated request redirects from departments list', function () {
    $this->get('/hr/departments')->assertRedirect();
});

it('tenant without permission gets 403 on departments list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/departments')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a department record', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Hr\Resources\DepartmentResource\Pages\CreateDepartment::class)
        ->fillForm(['name' => 'Sales'])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Department::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Sales')
        ->exists()
    )->toBeTrue();
});

// ---------- Edit ----------

it('can update an existing department', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Hr\Resources\DepartmentResource\Pages\EditDepartment::class,
            ['record' => $this->dept->getRouteKey()]
        )
        ->fillForm(['name' => 'Engineering Updated'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->dept->fresh()->name)->toBe('Engineering Updated');
});

// ---------- Delete ----------

it('can soft-delete a department', function () {
    $this->actingAs($this->tenant, 'tenant');

    $this->dept->delete();

    expect($this->dept->trashed())->toBeTrue();
    expect(Department::withTrashed()->withoutGlobalScopes()->find($this->dept->id))->not->toBeNull();
});

it('soft-deleted departments do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');

    $this->dept->delete();

    $visible = Department::all();
    expect($visible->pluck('id'))->not->toContain($this->dept->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see departments from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.departments.view');

    $this->actingAs($otherTenant, 'tenant');

    $visible = Department::all();
    expect($visible->pluck('id'))->not->toContain($this->dept->id);
});
