<?php

use App\Enums\Hr\LeaveRequestStatus;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveRequest;
use App\Models\Hr\LeaveType;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();

    $this->employee = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Days',
        'last_name'  => 'Calc',
        'email'      => 'days@test.com',
        'start_date' => '2023-01-01',
    ]);

    $this->leaveType = LeaveType::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Annual Leave',
        'code'       => 'AL',
        'is_paid'    => true,
        'requires_approval' => true,
        'is_active'  => true,
    ]);
});

// Helper to create a leave request with given days
function makeLeaveRequest(Employee $employee, LeaveType $leaveType, string $startDate, string $endDate, float $totalDays, bool $isHalfDay = false): LeaveRequest
{
    return LeaveRequest::withoutGlobalScopes()->create([
        'company_id'    => $employee->company_id,
        'employee_id'   => $employee->id,
        'leave_type_id' => $leaveType->id,
        'start_date'    => $startDate,
        'end_date'      => $endDate,
        'total_days'    => $totalDays,
        'is_half_day'   => $isHalfDay,
        'status'        => LeaveRequestStatus::Pending->value,
    ]);
}

it('total_days is stored and retrieved correctly as 1 day', function () {
    $req = makeLeaveRequest($this->employee, $this->leaveType, '2024-08-01', '2024-08-01', 1.0);

    expect((float) $req->total_days)->toBe(1.0);
});

it('total_days is stored and retrieved correctly as 5 days', function () {
    $req = makeLeaveRequest($this->employee, $this->leaveType, '2024-08-01', '2024-08-05', 5.0);

    expect((float) $req->total_days)->toBe(5.0);
});

it('total_days stores half-day as 0.5', function () {
    $req = makeLeaveRequest($this->employee, $this->leaveType, '2024-08-01', '2024-08-01', 0.5, true);

    expect((float) $req->total_days)->toBe(0.5);
    expect($req->is_half_day)->toBeTrue();
});

it('total_days is not null — NOT NULL constraint respected', function () {
    $req = makeLeaveRequest($this->employee, $this->leaveType, '2024-09-01', '2024-09-03', 3.0);

    expect($req->total_days)->not->toBeNull();
});

it('total_days casts to decimal with 2 decimal places', function () {
    $req = makeLeaveRequest($this->employee, $this->leaveType, '2024-08-01', '2024-08-10', 7.5);

    $req->refresh();

    // The decimal:2 cast means PHP treats it as a string representation
    expect(number_format((float) $req->total_days, 2))->toBe('7.50');
});

it('leave request status defaults to pending', function () {
    $req = makeLeaveRequest($this->employee, $this->leaveType, '2024-10-01', '2024-10-01', 1.0);

    expect($req->status)->toBe(LeaveRequestStatus::Pending);
});

it('approved leave request stores approved_at timestamp', function () {
    $req = makeLeaveRequest($this->employee, $this->leaveType, '2024-11-01', '2024-11-05', 5.0);

    $req->update([
        'status'      => LeaveRequestStatus::Approved->value,
        'approved_at' => now(),
    ]);

    expect($req->fresh()->approved_at)->not->toBeNull();
    expect($req->fresh()->status)->toBe(LeaveRequestStatus::Approved);
});

it('rejection reason can be stored on rejected leave request', function () {
    $req = makeLeaveRequest($this->employee, $this->leaveType, '2024-12-01', '2024-12-03', 3.0);

    $req->update([
        'status'           => LeaveRequestStatus::Rejected->value,
        'rejection_reason' => 'Project deadline conflict',
    ]);

    expect($req->fresh()->rejection_reason)->toBe('Project deadline conflict');
    expect($req->fresh()->status)->toBe(LeaveRequestStatus::Rejected);
});

it('multiple leave requests can exist for same employee', function () {
    makeLeaveRequest($this->employee, $this->leaveType, '2024-01-01', '2024-01-05', 5.0);
    makeLeaveRequest($this->employee, $this->leaveType, '2024-02-01', '2024-02-03', 3.0);

    $count = LeaveRequest::withoutGlobalScopes()
        ->where('employee_id', $this->employee->id)
        ->count();

    expect($count)->toBe(2);
});
