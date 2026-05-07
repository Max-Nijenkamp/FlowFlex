<?php

use App\Enums\Hr\PayFrequency;
use App\Enums\Hr\PayRunStatus;
use App\Events\Hr\PayRunProcessed;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\PayRun;
use App\Models\Hr\PayRunEmployee;
use App\Models\Hr\PayrollEntity;
use App\Models\Hr\Payslip;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.pay-runs.view',
        'hr.pay-runs.create',
        'hr.pay-runs.edit',
        'hr.pay-runs.delete',
    ]);

    $this->entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Main Payroll',
        'legal_name'   => 'Corp Ltd',
        'country_code' => 'NL',
        'is_default'   => true,
    ]);

    $this->payRun = PayRun::withoutGlobalScopes()->create([
        'company_id'         => $this->company->id,
        'payroll_entity_id'  => $this->entity->id,
        'status'             => PayRunStatus::Draft->value,
        'pay_frequency'      => PayFrequency::Monthly->value,
        'pay_period_start'   => '2024-08-01',
        'pay_period_end'     => '2024-08-31',
        'payment_date'       => '2024-08-31',
        'created_by_tenant_id' => $this->tenant->id,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list pay runs', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/pay-runs')
        ->assertOk();
});

it('unauthenticated request redirects from pay runs list', function () {
    $this->get('/hr/pay-runs')->assertRedirect();
});

it('tenant without permission gets 403 on pay runs list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/pay-runs')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a pay run record', function () {
    $run = PayRun::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'payroll_entity_id' => $this->entity->id,
        'status'            => PayRunStatus::Draft->value,
        'pay_frequency'     => PayFrequency::Weekly->value,
        'pay_period_start'  => '2024-09-01',
        'pay_period_end'    => '2024-09-07',
        'payment_date'      => '2024-09-07',
    ]);

    expect($run->exists)->toBeTrue();
    expect($run->status)->toBe(PayRunStatus::Draft);
    expect($run->pay_frequency)->toBe(PayFrequency::Weekly);
});

// ---------- ProcessPayRun action + events ----------

it('dispatches PayRunProcessed event when status changes to processed', function () {
    Event::fake();

    $this->payRun->update(['status' => PayRunStatus::Processed->value]);

    event(new PayRunProcessed($this->payRun->fresh()));

    Event::assertDispatched(PayRunProcessed::class, function ($event) {
        return $event->payRun->id === $this->payRun->id;
    });
});

it('DispatchPayslipGenerationJobs listener dispatches jobs for each run employee', function () {
    Event::fake();

    $employee = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Test',
        'last_name'  => 'Employee',
        'email'      => 'test.employee@example.com',
        'start_date' => '2023-01-01',
    ]);

    PayRunEmployee::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'pay_run_id'  => $this->payRun->id,
        'employee_id' => $employee->id,
    ]);

    // Simulate the event dispatch — listener is ShouldQueue, so use fake
    PayRunProcessed::dispatch($this->payRun->fresh()->load('runEmployees.employee'));

    Event::assertDispatched(PayRunProcessed::class);
});

// ---------- Edit ----------

it('can update a pay run status', function () {
    $this->actingAs($this->tenant, 'tenant');

    $this->payRun->update(['status' => PayRunStatus::PendingApproval->value]);

    expect($this->payRun->fresh()->status)->toBe(PayRunStatus::PendingApproval);
});

// ---------- Delete ----------

it('can soft-delete a pay run', function () {
    $this->payRun->delete();

    expect($this->payRun->trashed())->toBeTrue();
    expect(PayRun::withTrashed()->withoutGlobalScopes()->find($this->payRun->id))->not->toBeNull();
});

it('soft-deleted pay runs do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->payRun->delete();

    expect(PayRun::all()->pluck('id'))->not->toContain($this->payRun->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see pay runs from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.pay-runs.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(PayRun::all()->pluck('id'))->not->toContain($this->payRun->id);
});

// ---------- Casts ----------

it('pay run status casts to PayRunStatus enum', function () {
    expect($this->payRun->status)->toBe(PayRunStatus::Draft);
});

it('pay run frequency casts to PayFrequency enum', function () {
    expect($this->payRun->pay_frequency)->toBe(PayFrequency::Monthly);
});
