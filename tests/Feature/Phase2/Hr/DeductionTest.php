<?php

use App\Models\Hr\Deduction;
use App\Models\Hr\Employee;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.payroll.view',
        'hr.payroll.create',
        'hr.payroll.edit',
        'hr.payroll.delete',
    ]);

    $this->employee = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Deduction',
        'last_name'  => 'User',
        'email'      => 'deduction@test.com',
        'start_date' => '2024-01-01',
    ]);

    $this->deduction = Deduction::withoutGlobalScopes()->create([
        'company_id'     => $this->company->id,
        'employee_id'    => $this->employee->id,
        'name'           => 'Pension',
        'deduction_type' => 'fixed',
        'amount'         => '100.00',
        'is_percentage'  => false,
        'is_recurring'   => true,
        'effective_from' => now()->startOfMonth()->format('Y-m-d'),
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list deductions', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/deductions')
        ->assertOk();
});

it('unauthenticated request redirects from deductions', function () {
    $this->get('/hr/deductions')->assertRedirect();
});

it('tenant without permission gets 403 on deductions', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/deductions')
        ->assertForbidden();
});

// ---------- Model ----------

it('deduction has ULID primary key', function () {
    expect($this->deduction->id)->toBeString()->toHaveLength(26);
});

it('deduction belongs to correct company', function () {
    expect($this->deduction->company_id)->toBe($this->company->id);
});

it('deduction has employee relation', function () {
    expect($this->deduction->employee->id)->toBe($this->employee->id);
});

it('deduction amount is decimal', function () {
    expect($this->deduction->amount)->toBe('100.00');
});

it('cross-company isolation: other tenant cannot see deductions', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    attachModule($otherCompany, 'hr', 'hr');
    givePermissions($otherTenant, ['hr.deductions.view']);

    $this->actingAs($otherTenant, 'tenant');

    $visible = Deduction::all();
    expect($visible->pluck('id')->contains($this->deduction->id))->toBeFalse();
});
