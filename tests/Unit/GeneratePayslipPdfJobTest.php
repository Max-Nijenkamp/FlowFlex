<?php

use App\Enums\Hr\PayFrequency;
use App\Enums\Hr\PayRunStatus;
use App\Events\Hr\PayslipGenerated;
use App\Jobs\Hr\GeneratePayslipPdf;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\PayRun;
use App\Models\Hr\PayrollEntity;
use App\Models\Hr\Payslip;
use Illuminate\Support\Facades\Event;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();

    $this->entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Test Payroll',
        'legal_name'   => 'Test Corp Ltd',
        'country_code' => 'NL',
        'is_default'   => true,
    ]);

    $this->payRun = PayRun::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'payroll_entity_id' => $this->entity->id,
        'status'            => PayRunStatus::Processed->value,
        'pay_frequency'     => PayFrequency::Monthly->value,
        'pay_period_start'  => '2024-08-01',
        'pay_period_end'    => '2024-08-31',
        'payment_date'      => '2024-08-31',
    ]);

    $this->employee = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Payslip',
        'last_name'  => 'TestUser',
        'email'      => 'payslip@test.com',
        'start_date' => '2023-01-01',
    ]);
});

it('GeneratePayslipPdf job creates a payslip record on first run', function () {
    Event::fake();

    $job = new GeneratePayslipPdf($this->payRun, $this->employee);
    $job->handle();

    $payslip = Payslip::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('pay_run_id', $this->payRun->id)
        ->where('employee_id', $this->employee->id)
        ->first();

    expect($payslip)->not->toBeNull();
    expect($payslip->status)->toBe('generated');
    expect($payslip->generated_at)->not->toBeNull();
});

it('GeneratePayslipPdf job dispatches PayslipGenerated event', function () {
    Event::fake();

    $job = new GeneratePayslipPdf($this->payRun, $this->employee);
    $job->handle();

    Event::assertDispatched(PayslipGenerated::class);
});

it('GeneratePayslipPdf job updates existing payslip on second run', function () {
    Event::fake();

    // First run
    $job = new GeneratePayslipPdf($this->payRun, $this->employee);
    $job->handle();

    // Second run
    $job2 = new GeneratePayslipPdf($this->payRun, $this->employee);
    $job2->handle();

    // Should still only be one payslip for this pay run + employee combination
    $count = Payslip::withoutGlobalScopes()
        ->where('pay_run_id', $this->payRun->id)
        ->where('employee_id', $this->employee->id)
        ->count();

    expect($count)->toBe(1);
});

it('GeneratePayslipPdf job sets correct period dates from pay run', function () {
    Event::fake();

    $job = new GeneratePayslipPdf($this->payRun, $this->employee);
    $job->handle();

    $payslip = Payslip::withoutGlobalScopes()
        ->where('pay_run_id', $this->payRun->id)
        ->where('employee_id', $this->employee->id)
        ->first();

    expect($payslip->period_start->toDateString())->toBe('2024-08-01');
    expect($payslip->period_end->toDateString())->toBe('2024-08-31');
});

it('GeneratePayslipPdf job implements ShouldQueue', function () {
    $job = new GeneratePayslipPdf($this->payRun, $this->employee);

    expect($job)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});
