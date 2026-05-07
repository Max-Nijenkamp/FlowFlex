<?php

use App\Enums\Hr\PayFrequency;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\SalaryRecord;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.salary-records.view',
        'hr.salary-records.create',
        'hr.salary-records.edit',
        'hr.salary-records.delete',
    ]);

    $this->employee = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Carol',
        'last_name'  => 'Salary',
        'email'      => 'carol@test.com',
        'start_date' => '2023-01-01',
    ]);

    $this->salaryRecord = SalaryRecord::withoutGlobalScopes()->create([
        'company_id'           => $this->company->id,
        'employee_id'          => $this->employee->id,
        'salary_encrypted'     => 50000,
        'currency'             => 'EUR',
        'pay_frequency'        => PayFrequency::Monthly->value,
        'effective_from'       => '2024-01-01',
        'created_by_tenant_id' => $this->tenant->id,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list salary records', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/salary-records')
        ->assertOk();
});

it('unauthenticated request redirects from salary records list', function () {
    $this->get('/hr/salary-records')->assertRedirect();
});

it('tenant without permission gets 403 on salary records list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/salary-records')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a salary record', function () {
    $record = SalaryRecord::withoutGlobalScopes()->create([
        'company_id'       => $this->company->id,
        'employee_id'      => $this->employee->id,
        'salary_encrypted' => 60000,
        'currency'         => 'EUR',
        'pay_frequency'    => PayFrequency::Monthly->value,
        'effective_from'   => '2024-07-01',
    ]);

    expect($record->exists)->toBeTrue();
    expect($record->currency)->toBe('EUR');
});

// ---------- Edit ----------

it('can update a salary record', function () {
    $this->salaryRecord->update(['currency' => 'GBP']);

    expect($this->salaryRecord->fresh()->currency)->toBe('GBP');
});

// ---------- Delete ----------

it('can soft-delete a salary record', function () {
    $this->salaryRecord->delete();

    expect($this->salaryRecord->trashed())->toBeTrue();
    expect(SalaryRecord::withTrashed()->withoutGlobalScopes()->find($this->salaryRecord->id))->not->toBeNull();
});

it('soft-deleted salary records do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->salaryRecord->delete();

    expect(SalaryRecord::all()->pluck('id'))->not->toContain($this->salaryRecord->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see salary records from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.salary-records.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(SalaryRecord::all()->pluck('id'))->not->toContain($this->salaryRecord->id);
});

// ---------- Encrypted field ----------

it('salary_encrypted field is stored and cast as encrypted', function () {
    // The model casts salary_encrypted as 'encrypted', so reading it should return the original value
    $this->salaryRecord->refresh();

    expect($this->salaryRecord->salary_encrypted)->toBe('50000');
});

it('pay_frequency casts to PayFrequency enum', function () {
    expect($this->salaryRecord->pay_frequency)->toBe(PayFrequency::Monthly);
});
