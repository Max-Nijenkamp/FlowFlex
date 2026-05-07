<?php

use App\Models\Hr\ContractorPayment;
use App\Models\Hr\Employee;
use App\Models\Hr\PayRun;
use App\Models\Hr\PayrollEntity;
use App\Enums\Hr\PayRunStatus;
use App\Enums\Hr\PayFrequency;

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
        'first_name' => 'Contractor',
        'last_name'  => 'Smith',
        'email'      => 'contractor@test.com',
        'start_date' => '2024-01-01',
    ]);

    $this->payment = ContractorPayment::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'employee_id' => $this->employee->id,
        'amount'      => '5000.00',
        'currency'    => 'EUR',
        'reference'   => 'REF-001',
        'status'      => 'pending',
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list contractor payments', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/contractor-payments')
        ->assertOk();
});

it('unauthenticated request redirects from contractor payments', function () {
    $this->get('/hr/contractor-payments')->assertRedirect();
});

it('tenant without permission gets 403 on contractor payments', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/contractor-payments')
        ->assertForbidden();
});

it('authenticated tenant with correct permission can access contractor payments', function () {
    $other = makeTenant($this->company);
    givePermissions($other, ['hr.payroll.view']);

    $this->actingAs($other, 'tenant')
        ->get('/hr/contractor-payments')
        ->assertOk();
});

// ---------- Model ----------

it('contractor payment has ULID primary key', function () {
    expect($this->payment->id)->toBeString()->toHaveLength(26);
});

it('contractor payment belongs to correct company', function () {
    expect($this->payment->company_id)->toBe($this->company->id);
});

it('contractor payment has employee relation', function () {
    expect($this->payment->employee->id)->toBe($this->employee->id);
});

it('contractor payment amount is decimal', function () {
    expect($this->payment->amount)->toBe('5000.00');
});

it('tenant from different company cannot see contractor payments', function () {
    $otherCompany  = makeCompany();
    $otherTenant   = makeTenant($otherCompany);
    attachModule($otherCompany, 'hr', 'hr');
    givePermissions($otherTenant, ['hr.contractor-payments.view']);

    givePermissions($otherTenant, ['hr.payroll.view']);

    $this->actingAs($otherTenant, 'tenant')
        ->get('/hr/contractor-payments')
        ->assertOk();

    // The other company's contractor payment should not appear — global scope filters by company_id
    $visiblePayments = ContractorPayment::all();
    expect($visiblePayments->pluck('id')->contains($this->payment->id))->toBeFalse();
});
