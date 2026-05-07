<?php

use App\Enums\Hr\LeaveRequestStatus;
use App\Enums\Hr\OnboardingFlowStatus;
use App\Enums\Hr\PayFrequency;
use App\Enums\Hr\PayRunStatus;
use App\Events\Hr\LeaveApproved;
use App\Events\Hr\LeaveRejected;
use App\Events\Hr\LeaveRequested;
use App\Events\Hr\OnboardingStarted;
use App\Events\Hr\PayRunProcessed;
use App\Events\Hr\PayslipGenerated;
use App\Jobs\Hr\GeneratePayslipPdf;
use App\Listeners\Hr\DispatchPayslipGenerationJobs;
use App\Listeners\Hr\NotifyEmployeeLeaveApproved;
use App\Listeners\Hr\NotifyEmployeeLeaveRejected;
use App\Listeners\Hr\NotifyEmployeeOnboardingStarted;
use App\Listeners\Hr\NotifyEmployeePayslipGenerated;
use App\Listeners\Hr\NotifyManagerOfLeaveRequest;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveRequest;
use App\Models\Hr\LeaveType;
use App\Models\Hr\OnboardingFlow;
use App\Models\Hr\OnboardingTemplate;
use App\Models\Hr\PayRun;
use App\Models\Hr\PayRunEmployee;
use App\Models\Hr\PayrollEntity;
use App\Models\Hr\Payslip;
use App\Models\Tenant;
use App\Notifications\Hr\LeaveApprovedNotification;
use App\Notifications\Hr\LeaveRejectedNotification;
use App\Notifications\Hr\LeaveRequestedNotification;
use App\Notifications\Hr\OnboardingStartedNotification;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// Shared setup
beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    $this->manager = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Manager',
        'last_name'  => 'Boss',
        'email'      => 'manager@test.com',
        'start_date' => '2020-01-01',
    ]);

    $this->employee = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Employee',
        'last_name'  => 'Worker',
        'email'      => 'worker@test.com',
        'start_date' => '2023-01-01',
        'manager_id' => $this->manager->id,
    ]);

    $this->leaveType = LeaveType::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Annual Leave',
        'code'       => 'AL',
        'is_paid'    => true,
        'requires_approval' => true,
        'is_active'  => true,
    ]);

    $this->leaveRequest = LeaveRequest::withoutGlobalScopes()->create([
        'company_id'    => $this->company->id,
        'employee_id'   => $this->employee->id,
        'leave_type_id' => $this->leaveType->id,
        'start_date'    => '2024-09-01',
        'end_date'      => '2024-09-05',
        'total_days'    => 5,
        'status'        => LeaveRequestStatus::Pending->value,
    ]);
});

// ============================================================
// LeaveRequested → NotifyManagerOfLeaveRequest
// ============================================================

it('LeaveRequested event is dispatched correctly', function () {
    Event::fake();

    LeaveRequested::dispatch($this->leaveRequest);

    Event::assertDispatched(LeaveRequested::class, function ($event) {
        return $event->leaveRequest->id === $this->leaveRequest->id;
    });
});

it('EventServiceProvider maps LeaveRequested to NotifyManagerOfLeaveRequest', function () {
    Event::fake();

    event(new LeaveRequested($this->leaveRequest));

    Event::assertListening(LeaveRequested::class, NotifyManagerOfLeaveRequest::class);
});

it('NotifyManagerOfLeaveRequest sends notification to manager tenant', function () {
    Notification::fake();

    // Create a tenant matching the manager's email so the listener can resolve them
    $managerTenant = makeTenant($this->company, [
        'email' => 'manager@test.com',
    ]);

    $listener = new NotifyManagerOfLeaveRequest();
    $listener->handle(new LeaveRequested($this->leaveRequest));

    Notification::assertSentTo($managerTenant, LeaveRequestedNotification::class);
});

it('NotifyManagerOfLeaveRequest does nothing when employee has no manager', function () {
    Notification::fake();

    $this->employee->update(['manager_id' => null]);

    $listener = new NotifyManagerOfLeaveRequest();
    $listener->handle(new LeaveRequested($this->leaveRequest->fresh()));

    Notification::assertNothingSent();
});

// ============================================================
// LeaveApproved → NotifyEmployeeLeaveApproved
// ============================================================

it('LeaveApproved event is dispatched correctly', function () {
    Event::fake();

    LeaveApproved::dispatch($this->leaveRequest);

    Event::assertDispatched(LeaveApproved::class);
});

it('EventServiceProvider maps LeaveApproved to NotifyEmployeeLeaveApproved', function () {
    Event::fake();

    event(new LeaveApproved($this->leaveRequest));

    Event::assertListening(LeaveApproved::class, NotifyEmployeeLeaveApproved::class);
});

it('NotifyEmployeeLeaveApproved sends notification to employee tenant', function () {
    Notification::fake();

    // Create a tenant with the employee's email
    $employeeTenant = makeTenant($this->company, [
        'email' => 'worker@test.com',
    ]);

    $listener = new NotifyEmployeeLeaveApproved();
    $listener->handle(new LeaveApproved($this->leaveRequest));

    Notification::assertSentTo($employeeTenant, LeaveApprovedNotification::class);
});

// ============================================================
// LeaveRejected → NotifyEmployeeLeaveRejected
// ============================================================

it('LeaveRejected event is dispatched correctly', function () {
    Event::fake();

    LeaveRejected::dispatch($this->leaveRequest);

    Event::assertDispatched(LeaveRejected::class);
});

it('EventServiceProvider maps LeaveRejected to NotifyEmployeeLeaveRejected', function () {
    Event::fake();

    event(new LeaveRejected($this->leaveRequest));

    Event::assertListening(LeaveRejected::class, NotifyEmployeeLeaveRejected::class);
});

it('NotifyEmployeeLeaveRejected sends notification to employee tenant', function () {
    Notification::fake();

    $employeeTenant = makeTenant($this->company, [
        'email' => 'worker@test.com',
    ]);

    $listener = new NotifyEmployeeLeaveRejected();
    $listener->handle(new LeaveRejected($this->leaveRequest));

    Notification::assertSentTo($employeeTenant, LeaveRejectedNotification::class);
});

// ============================================================
// OnboardingStarted → NotifyEmployeeOnboardingStarted
// ============================================================

it('OnboardingStarted event is dispatched correctly', function () {
    Event::fake();

    $template = OnboardingTemplate::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Standard',
        'is_active'  => true,
    ]);

    $flow = OnboardingFlow::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'employee_id' => $this->employee->id,
        'template_id' => $template->id,
        'status'      => OnboardingFlowStatus::NotStarted->value,
    ]);

    OnboardingStarted::dispatch($flow);

    Event::assertDispatched(OnboardingStarted::class, fn ($e) => $e->flow->id === $flow->id);
});

it('EventServiceProvider maps OnboardingStarted to NotifyEmployeeOnboardingStarted', function () {
    Event::fake();

    $template = OnboardingTemplate::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Standard',
        'is_active'  => true,
    ]);

    $flow = OnboardingFlow::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'employee_id' => $this->employee->id,
        'template_id' => $template->id,
        'status'      => OnboardingFlowStatus::NotStarted->value,
    ]);

    event(new OnboardingStarted($flow));

    Event::assertListening(OnboardingStarted::class, NotifyEmployeeOnboardingStarted::class);
});

// ============================================================
// PayRunProcessed → DispatchPayslipGenerationJobs
// ============================================================

it('PayRunProcessed event is dispatched correctly', function () {
    Event::fake();

    $entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Main',
        'legal_name'   => 'Corp Ltd',
        'country_code' => 'NL',
        'is_default'   => true,
    ]);

    $payRun = PayRun::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'payroll_entity_id' => $entity->id,
        'status'            => PayRunStatus::Processed->value,
        'pay_frequency'     => PayFrequency::Monthly->value,
        'pay_period_start'  => '2024-08-01',
        'pay_period_end'    => '2024-08-31',
        'payment_date'      => '2024-08-31',
    ]);

    PayRunProcessed::dispatch($payRun);

    Event::assertDispatched(PayRunProcessed::class);
});

it('DispatchPayslipGenerationJobs listener dispatches GeneratePayslipPdf job per employee', function () {
    Queue::fake();

    $entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Main',
        'legal_name'   => 'Corp',
        'country_code' => 'NL',
        'is_default'   => true,
    ]);

    $payRun = PayRun::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'payroll_entity_id' => $entity->id,
        'status'            => PayRunStatus::Processed->value,
        'pay_frequency'     => PayFrequency::Monthly->value,
        'pay_period_start'  => '2024-08-01',
        'pay_period_end'    => '2024-08-31',
        'payment_date'      => '2024-08-31',
    ]);

    PayRunEmployee::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'pay_run_id'  => $payRun->id,
        'employee_id' => $this->employee->id,
    ]);

    $listener = new DispatchPayslipGenerationJobs();
    $listener->handle(new PayRunProcessed($payRun->load('runEmployees.employee')));

    Queue::assertPushed(GeneratePayslipPdf::class);
});

it('EventServiceProvider maps PayRunProcessed to DispatchPayslipGenerationJobs', function () {
    Event::fake();

    $entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Main',
        'legal_name'   => 'Corp',
        'country_code' => 'NL',
        'is_default'   => true,
    ]);

    $payRun = PayRun::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'payroll_entity_id' => $entity->id,
        'status'            => PayRunStatus::Processed->value,
        'pay_frequency'     => PayFrequency::Monthly->value,
        'pay_period_start'  => '2024-08-01',
        'pay_period_end'    => '2024-08-31',
        'payment_date'      => '2024-08-31',
    ]);

    event(new PayRunProcessed($payRun));

    Event::assertListening(PayRunProcessed::class, DispatchPayslipGenerationJobs::class);
});

// ============================================================
// PayslipGenerated → NotifyEmployeePayslipGenerated
// ============================================================

it('PayslipGenerated event is dispatched correctly', function () {
    Event::fake();

    $entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Main',
        'legal_name'   => 'Corp',
        'country_code' => 'NL',
        'is_default'   => true,
    ]);

    $payRun = PayRun::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'payroll_entity_id' => $entity->id,
        'status'            => PayRunStatus::Processed->value,
        'pay_frequency'     => PayFrequency::Monthly->value,
        'pay_period_start'  => '2024-08-01',
        'pay_period_end'    => '2024-08-31',
        'payment_date'      => '2024-08-31',
    ]);

    $payslip = Payslip::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'pay_run_id'   => $payRun->id,
        'employee_id'  => $this->employee->id,
        'period_start' => '2024-08-01',
        'period_end'   => '2024-08-31',
        'status'       => 'generated',
        'generated_at' => now(),
    ]);

    PayslipGenerated::dispatch($payslip);

    Event::assertDispatched(PayslipGenerated::class);
});

it('EventServiceProvider maps PayslipGenerated to NotifyEmployeePayslipGenerated', function () {
    Event::fake();

    $entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Main',
        'legal_name'   => 'Corp',
        'country_code' => 'NL',
        'is_default'   => true,
    ]);

    $payRun = PayRun::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'payroll_entity_id' => $entity->id,
        'status'            => PayRunStatus::Processed->value,
        'pay_frequency'     => PayFrequency::Monthly->value,
        'pay_period_start'  => '2024-08-01',
        'pay_period_end'    => '2024-08-31',
        'payment_date'      => '2024-08-31',
    ]);

    $payslip = Payslip::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'pay_run_id'   => $payRun->id,
        'employee_id'  => $this->employee->id,
        'period_start' => '2024-08-01',
        'period_end'   => '2024-08-31',
        'status'       => 'generated',
        'generated_at' => now(),
    ]);

    event(new PayslipGenerated($payslip));

    Event::assertListening(PayslipGenerated::class, NotifyEmployeePayslipGenerated::class);
});
