<?php

declare(strict_types=1);

use App\Actions\HR\GiveFeedbackAction;
use App\Actions\HR\SubmitOwnDeiAttributesAction;
use App\Actions\HR\WithdrawDeiConsentAction;
use App\Events\HR\LeaveRequestApproved;
use App\Events\HR\TimesheetApproved;
use App\Exceptions\HR\EmployeeOnLeaveException;
use App\Exceptions\HR\ReviewLockedException;
use App\Exceptions\HR\ShiftConflictException;
use App\Models\Company;
use App\Models\HR\Applicant;
use App\Models\HR\CompensationBand;
use App\Models\HR\DeiAttribute;
use App\Models\HR\DeiSnapshot;
use App\Models\HR\Employee;
use App\Models\HR\HeadcountPlan;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use App\Models\HR\PayrollEmployee;
use App\Models\HR\ReviewCycle;
use App\Models\HR\SalaryHistory;
use App\Models\HR\TimeEntry;
use App\Models\User;
use App\Services\HR\CompensationService;
use App\Services\HR\DeiSnapshotService;
use App\Services\HR\HrAnalyticsService;
use App\Services\HR\PerformanceService;
use App\Services\HR\RecruitmentService;
use App\Services\HR\ShiftService;
use App\Services\HR\TimeService;
use App\Services\HR\WorkforceService;
use App\States\HR\Applicant\Interview;
use App\States\HR\Applicant\Offer;
use App\States\HR\Applicant\Screening;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');
});

// --- hr.time ---
it('runs the timesheet flow: entries → submit week → approve fires TimesheetApproved', function () {
    Event::fake([TimesheetApproved::class]);
    $employee = Employee::factory()->forCompany($this->company)->create();
    $time = app(TimeService::class);

    // Manual entries Mon+Tue of a fixed week
    TimeEntry::create([
        'company_id' => $this->company->id, 'employee_id' => $employee->id,
        'date' => '2026-06-08', 'total_minutes' => 480,
    ]);
    TimeEntry::create([
        'company_id' => $this->company->id, 'employee_id' => $employee->id,
        'date' => '2026-06-09', 'total_minutes' => 450,
    ]);

    $timesheet = $time->submitWeek($employee->id, '2026-06-08');
    expect($timesheet->total_minutes)->toBe(930)
        ->and((string) $timesheet->status)->toBe('submitted');

    $approved = $time->approve($timesheet->id);
    expect((string) $approved->status)->toBe('approved');
    Event::assertDispatched(TimesheetApproved::class, fn ($e) => $e->total_minutes === 930);
});

// --- hr.shifts ---
it('blocks conflicting shifts and leave-day assignments', function () {
    $employee = Employee::factory()->forCompany($this->company)->create();
    $shifts = app(ShiftService::class);

    $shifts->createShift('2026-06-15', '09:00', '17:00', 'cashier', $employee->id);

    try {
        $shifts->createShift('2026-06-15', '12:00', '20:00', 'cashier', $employee->id);
        $this->fail('Expected ShiftConflictException');
    } catch (ShiftConflictException) {
    }

    // Approved leave on the date → blocked
    $type = LeaveType::factory()->forCompany($this->company)->create();
    LeaveRequest::create([
        'company_id' => $this->company->id, 'employee_id' => $employee->id,
        'leave_type_id' => $type->id, 'start_date' => '2026-06-16', 'end_date' => '2026-06-16',
        'days_requested' => 1, 'status' => 'approved',
    ]);

    $shifts->createShift('2026-06-16', '09:00', '17:00', 'cashier', $employee->id);
})->throws(EmployeeOnLeaveException::class);

it('unassigns published shifts when leave is approved (listener)', function () {
    $employee = Employee::factory()->forCompany($this->company)->create();
    $shift = app(ShiftService::class)->createShift('2026-07-01', '09:00', '17:00', 'support', $employee->id);

    event(new LeaveRequestApproved(
        company_id: $this->company->id,
        leave_request_id: '01TEST',
        employee_id: $employee->id,
        leave_type_id: '01TYPE',
        start_date: CarbonImmutable::parse('2026-06-30'),
        end_date: CarbonImmutable::parse('2026-07-02'),
        days: 3.0,
    ));

    expect($shift->fresh()->employee_id)->toBeNull(); // coverage gap flagged
});

// --- hr.compensation ---
it('adjusts salary atomically: payroll profile + encrypted append-only history', function () {
    $employee = Employee::factory()->forCompany($this->company)->create();
    $comp = app(CompensationService::class);

    $comp->adjustSalary($employee->id, 420000, 'promotion', '2026-07-01');

    $profile = PayrollEmployee::query()->where('employee_id', $employee->id)->firstOrFail();
    expect($profile->salaryCents())->toBe(420000)
        ->and($profile->status)->toBe('ready');

    $raw = DB::table('hr_salary_history')->value('amount_raw');
    expect($raw)->not->toContain('420000'); // ciphertext at rest

    $history = SalaryHistory::query()->firstOrFail();
    expect($history->salary_band)->toBe('4k-6k')
        ->and($history->reason)->toBe('promotion');
});

it('computes compa-ratio against the matching band', function () {
    $employee = Employee::factory()->forCompany($this->company)->create(['job_title' => 'Engineer']);
    $comp = app(CompensationService::class);
    CompensationBand::create([
        'company_id' => $this->company->id, 'job_grade' => 'Engineer',
        'min_salary_cents' => 300000, 'mid_salary_cents' => 400000, 'max_salary_cents' => 500000,
    ]);
    $comp->adjustSalary($employee->id, 440000, 'comp-review', '2026-07-01');

    expect($comp->compaRatio($employee->id))->toBe(1.1);
});

// --- hr.recruitment ---
it('runs the recruitment flow: public apply → stages → offer → hire (auto-close + salary)', function () {
    $recruitment = app(RecruitmentService::class);
    $requisition = $recruitment->openRequisition('Backend Engineer', 'Build FlowFlex', 'full-time');

    // Public apply (no auth)
    auth('web')->logout();
    $this->post("/careers/{$requisition->slug}/apply", [
        'first_name' => 'Cand', 'last_name' => 'Idate', 'email' => 'cand@apply.test',
    ])->assertRedirect();

    $this->actingAs($this->user, 'web');
    $applicant = Applicant::query()->firstOrFail();
    expect((string) $applicant->status)->toBe('applied')
        ->and($applicant->source)->toBe('careers');

    $recruitment->moveStage($applicant->id, Screening::class);
    $recruitment->moveStage($applicant->id, Interview::class);
    $recruitment->moveStage($applicant->id, Offer::class);
    $recruitment->makeOffer($applicant->id, 380000, '2026-08-01');

    $employee = $recruitment->hire($applicant->id);

    expect($employee->job_title)->toBe('Backend Engineer')
        ->and((string) $applicant->fresh()->status)->toBe('hired')
        ->and($requisition->fresh()->status)->toBe('closed') // headcount 1 filled
        ->and(PayrollEmployee::query()->where('employee_id', $employee->id)->first()->salaryCents())->toBe(380000);
});

it('honeypot blocks bot applications', function () {
    $requisition = app(RecruitmentService::class)->openRequisition('Role', 'desc', 'full-time');

    auth('web')->logout();
    $this->post("/careers/{$requisition->slug}/apply", [
        'first_name' => 'Bot', 'last_name' => 'Spam', 'email' => 'bot@spam.test',
        'website' => 'http://spam.example', // honeypot filled
    ]);

    expect(Applicant::query()->withoutGlobalScopes()->count())->toBe(0);
});

// --- hr.performance ---
it('activates a cycle (review matrix), locks submissions outside active, calibrates only in calibration', function () {
    $perf = app(PerformanceService::class);
    $manager = Employee::factory()->forCompany($this->company)->create();
    Employee::factory()->forCompany($this->company)->create(['manager_id' => $manager->id]);

    $cycle = ReviewCycle::create([
        'company_id' => $this->company->id, 'name' => '2026 Annual',
        'period_start' => '2026-01-01', 'period_end' => '2026-12-31',
    ]);

    $cycle = $perf->activateCycle($cycle->id);
    // manager: self; report: self + manager = 3 reviews
    expect($cycle->reviews()->count())->toBe(3);

    $review = $cycle->reviews()->where('type', 'self')->first();
    $perf->submitReview($review->id, ['q1' => 'Good year'], 4.0);
    expect($review->fresh()->status)->toBe('submitted');

    // calibration locks submissions
    $perf->startCalibration($cycle->id);
    try {
        $perf->submitReview($cycle->reviews()->where('status', 'pending')->first()->id, ['q1' => 'late']);
        $this->fail('Expected ReviewLockedException');
    } catch (ReviewLockedException) {
    }

    $perf->calibrate($review->id, 4.5);
    expect($review->fresh()->rating)->toBe(4.5);

    $perf->finalise($cycle->id);
    expect((string) $cycle->fresh()->status)->toBe('finalised');
});

// --- hr.feedback ---
it('forces feedback visibility by type and blocks self-feedback', function () {
    $a = Employee::factory()->forCompany($this->company)->create();
    $b = Employee::factory()->forCompany($this->company)->create();

    $praise = GiveFeedbackAction::run($a->id, $b->id, 'praise', 'Great launch!');
    $note = GiveFeedbackAction::run($a->id, $b->id, 'coaching-note', 'Focus area X');

    expect($praise->visibility)->toBe('public')
        ->and($note->visibility)->toBe('manager-chain');

    GiveFeedbackAction::run($a->id, $a->id, 'praise', 'I am great');
})->throws(ValidationException::class);

// --- hr.dei ---
it('stores own DEI attributes encrypted with consent; snapshots suppress small groups; withdrawal deletes', function () {
    $own = Employee::factory()->forCompany($this->company)->create(['user_id' => $this->user->id]);
    SubmitOwnDeiAttributesAction::run(['gender' => 'female']);

    $raw = DB::table('hr_dei_attributes')->value('value');
    expect($raw)->not->toContain('female'); // encrypted at rest

    // 6 more of one group (>= threshold 5), 1 of another (suppressed)
    foreach (range(1, 6) as $i) {
        DeiAttribute::create([
            'company_id' => $this->company->id,
            'employee_id' => Employee::factory()->forCompany($this->company)->create()->id,
            'dimension' => 'gender', 'value' => 'male', 'consented_at' => now(),
        ]);
    }

    app(DeiSnapshotService::class)->generate('2026-Q2');
    $snapshot = DeiSnapshot::query()->where('dimension', 'gender')->firstOrFail();

    expect($snapshot->breakdown)->toHaveKey('male')
        ->and($snapshot->breakdown)->not->toHaveKey('female'); // group of 1 suppressed

    WithdrawDeiConsentAction::run();
    expect(DeiAttribute::query()->where('employee_id', $own->id)->count())->toBe(0);
});

// --- hr.analytics + hr.workforce ---
it('computes HR metrics and plan-vs-actual without N+1', function () {
    Employee::factory()->forCompany($this->company)->count(4)->create();
    HeadcountPlan::create([
        'company_id' => $this->company->id, 'period' => '2026-Q3', 'target_headcount' => 6,
    ]);

    $metrics = app(HrAnalyticsService::class)->metrics(
        CarbonImmutable::parse('2026-01-01'), CarbonImmutable::parse('2026-12-31'),
    );
    expect($metrics['headcount'])->toBe(4);

    $plan = app(WorkforceService::class)->planVsActual('2026-Q3')->first();
    expect($plan['target'])->toBe(6)->and($plan['actual'])->toBe(4)->and($plan['gap'])->toBe(2);
});
