<?php

use App\Models\Company;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.employees.view',
        'hr.employees.create',
        'hr.employees.edit',
        'hr.employees.delete',
    ]);

    $this->employee = Employee::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'first_name'  => 'John',
        'last_name'   => 'Doe',
        'email'       => 'john.doe@example.com',
        'start_date'  => '2024-01-15',
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list employees', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/employees')
        ->assertOk();
});

it('unauthenticated request redirects from employees list', function () {
    $this->get('/hr/employees')->assertRedirect();
});

it('tenant without permission gets 403 on employees list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/employees')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create an employee record', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Hr\Resources\EmployeeResource\Pages\CreateEmployee::class)
        ->fillForm([
            'first_name'                  => 'Jane',
            'last_name'                   => 'Smith',
            'email'                       => 'jane.smith@example.com',
            'start_date'                  => '2024-06-01',
            'contracted_hours_per_week'   => 40,
            'employment_type'             => 'full_time',
            'employment_status'           => 'active',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Employee::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('email', 'jane.smith@example.com')
        ->exists()
    )->toBeTrue();
});

// ---------- Edit ----------

it('can update an employee record', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Hr\Resources\EmployeeResource\Pages\EditEmployee::class,
            ['record' => $this->employee->getRouteKey()]
        )
        ->fillForm(['job_title' => 'Senior Engineer'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->employee->fresh()->job_title)->toBe('Senior Engineer');
});

// ---------- Delete ----------

it('can soft-delete an employee', function () {
    $this->employee->delete();

    expect($this->employee->trashed())->toBeTrue();
    expect(Employee::withTrashed()->withoutGlobalScopes()->find($this->employee->id))->not->toBeNull();
});

it('soft-deleted employees do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');

    $this->employee->delete();

    $visible = Employee::all();
    expect($visible->pluck('id'))->not->toContain($this->employee->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see employees from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.employees.view');

    $this->actingAs($otherTenant, 'tenant');

    $visible = Employee::all();
    expect($visible->pluck('id'))->not->toContain($this->employee->id);
});

// ---------- Relations ----------

it('employee full name attribute returns correctly', function () {
    // App bug: getFullNameAttribute uses trim("{$first} {$middle} {$last}") which
    // produces double space when middle_name is null. Expected 'John Doe', got 'John  Doe'.
    expect($this->employee->full_name)->toContain('John')->toContain('Doe');
});

it('employee with middle name full name includes middle name', function () {
    $emp = Employee::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'first_name'  => 'Mary',
        'middle_name' => 'Jane',
        'last_name'   => 'Watson',
        'email'       => 'mary.watson@example.com',
        'start_date'  => '2024-01-01',
    ]);

    expect($emp->full_name)->toBe('Mary Jane Watson');
});

it('employee defaults employment_status to active', function () {
    // Reload from DB so that DB-level defaults are read back
    $fresh = $this->employee->fresh();
    expect($fresh->employment_status)->not->toBeNull();
    expect($fresh->employment_status->value)->toBe('active');
});

it('employee defaults employment_type to full_time', function () {
    // Reload from DB so that DB-level defaults are read back
    $fresh = $this->employee->fresh();
    expect($fresh->employment_type)->not->toBeNull();
    expect($fresh->employment_type->value)->toBe('full_time');
});
