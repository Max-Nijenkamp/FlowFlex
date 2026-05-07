<?php

use App\Enums\Hr\PayFrequency;
use App\Enums\Hr\PayRunStatus;
use App\Models\Hr\Employee;
use App\Models\Hr\PayRun;
use App\Models\Hr\PayRunEmployee;
use App\Models\Hr\PayrollEntity;
use App\Models\Hr\Payslip;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, ['hr.payroll.view']);

    $this->employee = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Payslip',
        'last_name'  => 'User',
        'email'      => 'payslip@test.com',
        'start_date' => '2024-01-01',
    ]);

    $this->entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Main Payroll',
        'legal_name'   => 'Corp Ltd',
        'country_code' => 'NL',
        'is_default'   => true,
    ]);

    $this->payRun = PayRun::withoutGlobalScopes()->create([
        'company_id'           => $this->company->id,
        'payroll_entity_id'    => $this->entity->id,
        'status'               => PayRunStatus::Processed->value,
        'pay_frequency'        => PayFrequency::Monthly->value,
        'pay_period_start'     => '2024-08-01',
        'pay_period_end'       => '2024-08-31',
        'payment_date'         => '2024-08-31',
        'created_by_tenant_id' => $this->tenant->id,
    ]);

    $this->payRunEmployee = PayRunEmployee::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'pay_run_id'  => $this->payRun->id,
        'employee_id' => $this->employee->id,
        'gross_pay'   => '5000.00',
        'net_pay'     => '3800.00',
    ]);

    $this->payslip = Payslip::withoutGlobalScopes()->create([
        'company_id'         => $this->company->id,
        'pay_run_id'         => $this->payRun->id,
        'employee_id'        => $this->employee->id,
        'pay_run_employee_id'=> $this->payRunEmployee->id,
        'period_start'       => '2024-08-01',
        'period_end'         => '2024-08-31',
        'status'             => 'generated',
        'generated_at'       => now(),
    ]);
});

// ---------- List (read-only resource) ----------

it('authenticated tenant with permission can list payslips', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/payslips')
        ->assertOk();
});

it('unauthenticated request redirects from payslips', function () {
    $this->get('/hr/payslips')->assertRedirect();
});

it('tenant without view permission gets 403 on payslips', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/payslips')
        ->assertForbidden();
});

// ---------- Model ----------

it('payslip has ULID primary key', function () {
    expect($this->payslip->id)->toBeString()->toHaveLength(26);
});

it('payslip belongs to correct company', function () {
    expect($this->payslip->company_id)->toBe($this->company->id);
});

it('payslip has employee relation', function () {
    expect($this->payslip->employee->id)->toBe($this->employee->id);
});

it('payslip has payRun relation', function () {
    expect($this->payslip->payRun->id)->toBe($this->payRun->id);
});

it('payslip does not expose pdf_path in fillable', function () {
    expect($this->payslip->getFillable())->not->toContain('pdf_path');
});

it('cross-company isolation: payslips scoped to company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    attachModule($otherCompany, 'hr', 'hr');
    givePermissions($otherTenant, ['hr.payslips.view']);

    $this->actingAs($otherTenant, 'tenant');

    $visible = Payslip::all();
    expect($visible->pluck('id')->contains($this->payslip->id))->toBeFalse();
});
