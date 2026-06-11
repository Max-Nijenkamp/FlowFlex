<?php

declare(strict_types=1);

use App\Contracts\HR\EmployeeServiceInterface;
use App\Contracts\HR\OnboardingServiceInterface;
use App\Contracts\HR\PayrollServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Data\HR\CreatePayrollRunData;
use App\Events\HR\PayrollRunApproved;
use App\Exceptions\HR\CannotApproveOwnRunException;
use App\Exceptions\HR\IncompletePayrollProfileException;
use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\OnboardingPlan;
use App\Models\HR\OnboardingTask;
use App\Models\HR\OnboardingTemplate;
use App\Models\HR\PayrollEmployee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');
});

it('hiring creates a payroll stub and starts the default onboarding plan (listeners)', function () {
    $template = OnboardingTemplate::factory()->forCompany($this->company)->create();
    OnboardingTask::create([
        'template_id' => $template->id,
        'company_id' => $this->company->id,
        'title' => 'Set up laptop',
        'assigned_role' => 'it',
        'order' => 1,
    ]);

    $employee = app(EmployeeServiceInterface::class)->hire(CreateEmployeeData::from([
        'first_name' => 'New', 'last_name' => 'Hire', 'email' => 'new@acme.test',
        'hire_date' => '2026-06-01', 'job_title' => 'Designer', 'employment_type' => 'full-time',
    ]));

    // Sync queue: listeners ran inline.
    $stub = PayrollEmployee::query()->where('employee_id', $employee->id)->first();
    expect($stub)->not->toBeNull()
        ->and($stub->status)->toBe('incomplete');

    $plan = OnboardingPlan::query()->where('employee_id', $employee->id)->first();
    expect($plan)->not->toBeNull()
        ->and($plan->tasks()->count())->toBe(1);
});

it('completes onboarding tasks and auto-closes the plan', function () {
    $service = app(OnboardingServiceInterface::class);
    $template = OnboardingTemplate::factory()->forCompany($this->company)->create();
    OnboardingTask::create([
        'template_id' => $template->id, 'company_id' => $this->company->id,
        'title' => 'Task', 'assigned_role' => 'hr', 'order' => 1,
    ]);
    $employee = Employee::factory()->forCompany($this->company)->create();

    $plan = $service->startPlan($this->company->id, $employee->id);
    expect($service->progress($plan->id))->toBe(0.0);

    $service->completeTask($plan->tasks()->first()->id);

    expect($service->progress($plan->id))->toBe(1.0)
        ->and($plan->fresh()->completed_at)->not->toBeNull();
});

it('runs payroll: process computes brick/money totals, approve fires the event', function () {
    Event::fake([PayrollRunApproved::class]);
    $payroll = app(PayrollServiceInterface::class);

    $employees = Employee::factory()->forCompany($this->company)->count(2)->create();
    foreach ($employees as $e) {
        PayrollEmployee::factory()->create([
            'company_id' => $this->company->id,
            'employee_id' => $e->id,
            'salary_raw' => '300000', // €3,000.00
        ]);
    }

    $run = $payroll->createRun(new CreatePayrollRunData(
        period_start: '2026-06-01',
        period_end: '2026-06-30',
        employee_ids: $employees->pluck('id')->all(),
    ));

    $run = $payroll->processRun($run->id);
    expect($run->total_gross_cents)->toBe(600000)
        ->and($run->payslips()->count())->toBe(2);

    // Four-eyes: creator cannot approve.
    try {
        $payroll->approveRun($run->id);
        $this->fail('Expected CannotApproveOwnRunException');
    } catch (CannotApproveOwnRunException) {
    }

    $second = User::factory()->forCompany($this->company)->create();
    $this->actingAs($second, 'web');
    $approved = $payroll->approveRun($run->id);

    expect((string) $approved->status)->toBe('approved');
    Event::assertDispatched(PayrollRunApproved::class, fn ($e) => $e->total_gross_cents === 600000
        && $e->currency === 'EUR');
});

it('blocks processing with incomplete payroll profiles', function () {
    $payroll = app(PayrollServiceInterface::class);
    $employee = Employee::factory()->forCompany($this->company)->create();
    PayrollEmployee::factory()->create([
        'company_id' => $this->company->id,
        'employee_id' => $employee->id,
        'status' => 'incomplete',
    ]);

    $run = $payroll->createRun(new CreatePayrollRunData(
        period_start: '2026-06-01', period_end: '2026-06-30', employee_ids: [$employee->id],
    ));

    $payroll->processRun($run->id);
})->throws(IncompletePayrollProfileException::class);

it('encrypts payslip amounts at rest', function () {
    $payroll = app(PayrollServiceInterface::class);
    $employee = Employee::factory()->forCompany($this->company)->create();
    PayrollEmployee::factory()->create([
        'company_id' => $this->company->id, 'employee_id' => $employee->id, 'salary_raw' => '250000',
    ]);
    $run = $payroll->createRun(new CreatePayrollRunData(
        period_start: '2026-06-01', period_end: '2026-06-30', employee_ids: [$employee->id],
    ));
    $payroll->processRun($run->id);

    $raw = DB::table('hr_payslips')->value('amounts_raw');
    expect($raw)->not->toContain('250000');

    $payslip = $run->payslips()->first();
    expect($payslip->amounts()['gross_cents'])->toBe(250000);
});
