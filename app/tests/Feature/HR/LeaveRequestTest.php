<?php

declare(strict_types=1);

use App\Contracts\HR\LeaveServiceInterface;
use App\Data\HR\SubmitLeaveRequestData;
use App\Events\HR\LeaveRequestApproved;
use App\Exceptions\HR\CannotApproveOwnRequestException;
use App\Exceptions\HR\InsufficientLeaveBalanceException;
use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\ModelStates\Exceptions\TransitionNotFound;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->service = app(LeaveServiceInterface::class);
    $this->employee = Employee::factory()->forCompany($this->company)->create();
    $this->type = LeaveType::factory()->forCompany($this->company)->create();
    LeaveBalance::factory()->create([
        'company_id' => $this->company->id,
        'employee_id' => $this->employee->id,
        'leave_type_id' => $this->type->id,
        'allocated_days' => 10,
        'year' => 2026,
    ]);
    $this->approver = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->approver, 'web');
});

function leaveData($test, array $overrides = []): SubmitLeaveRequestData
{
    // Mon 2026-06-15 .. Fri 2026-06-19 = 5 working days
    return SubmitLeaveRequestData::from(array_merge([
        'employee_id' => $test->employee->id,
        'leave_type_id' => $test->type->id,
        'start_date' => '2026-06-15',
        'end_date' => '2026-06-19',
    ], $overrides));
}

it('computes working days excluding weekends and moves balance to pending', function () {
    $request = $this->service->submit(leaveData($this, ['end_date' => '2026-06-22'])); // Mon..next Mon = 6 wd

    expect($request->days_requested)->toBe(6.0)
        ->and((string) $request->status)->toBe('submitted');

    $balance = LeaveBalance::query()->firstOrFail();
    expect($balance->pending_days)->toBe(6.0);
});

it('rejects submissions beyond the available balance', function () {
    $this->service->submit(leaveData($this, ['start_date' => '2026-06-01', 'end_date' => '2026-06-12'])); // 10 wd pending

    $this->service->submit(leaveData($this)); // 5 more — over the 10 allocated
})->throws(InsufficientLeaveBalanceException::class);

it('approves: state + balance pending→taken + LeaveRequestApproved event', function () {
    Event::fake([LeaveRequestApproved::class]);
    $request = $this->service->submit(leaveData($this));

    $approved = $this->service->approve($request->id);

    expect((string) $approved->status)->toBe('approved');
    $balance = LeaveBalance::query()->firstOrFail();
    expect($balance->pending_days)->toBe(0.0)->and($balance->taken_days)->toBe(5.0);

    Event::assertDispatched(LeaveRequestApproved::class, fn ($e) => $e->leave_request_id === $request->id
        && $e->days === 5.0);
});

it('blocks approving your own request', function () {
    $self = Employee::factory()->forCompany($this->company)->create(['user_id' => $this->approver->id]);
    LeaveBalance::factory()->create([
        'company_id' => $this->company->id,
        'employee_id' => $self->id,
        'leave_type_id' => $this->type->id,
        'allocated_days' => 10,
        'year' => 2026,
    ]);
    $request = $this->service->submit(leaveData($this, ['employee_id' => $self->id]));

    $this->service->approve($request->id);
})->throws(CannotApproveOwnRequestException::class);

it('auto-approves no-approval types on submit', function () {
    $auto = LeaveType::factory()->forCompany($this->company)->autoApprove()->create();
    LeaveBalance::factory()->create([
        'company_id' => $this->company->id,
        'employee_id' => $this->employee->id,
        'leave_type_id' => $auto->id,
        'allocated_days' => 10,
        'year' => 2026,
    ]);

    $request = $this->service->submit(leaveData($this, ['leave_type_id' => $auto->id]));

    expect((string) $request->status)->toBe('approved');
});

it('releases pending balance on rejection', function () {
    $request = $this->service->submit(leaveData($this));
    $this->service->reject($request->id, 'Coverage needed');

    $balance = LeaveBalance::query()->firstOrFail();
    expect($balance->pending_days)->toBe(0.0)
        ->and($balance->taken_days)->toBe(0.0)
        ->and($request->fresh()->rejection_reason)->toBe('Coverage needed');
});

it('rejects invalid transitions', function () {
    $request = $this->service->submit(leaveData($this));
    $this->service->approve($request->id);

    // approved → approved is not allowed
    $this->service->approve($request->id);
})->throws(TransitionNotFound::class);
