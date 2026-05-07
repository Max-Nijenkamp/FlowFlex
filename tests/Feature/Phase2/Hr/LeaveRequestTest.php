<?php

use App\Enums\Hr\LeaveRequestStatus;
use App\Events\Hr\LeaveApproved;
use App\Events\Hr\LeaveRejected;
use App\Events\Hr\LeaveRequested;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveRequest;
use App\Models\Hr\LeaveType;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.leave-requests.view',
        'hr.leave-requests.create',
        'hr.leave-requests.edit',
        'hr.leave-requests.delete',
    ]);

    $this->employee = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Alice',
        'last_name'  => 'Test',
        'email'      => 'alice@test.com',
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

    $this->leaveRequest = LeaveRequest::withoutGlobalScopes()->create([
        'company_id'    => $this->company->id,
        'employee_id'   => $this->employee->id,
        'leave_type_id' => $this->leaveType->id,
        'start_date'    => '2024-08-01',
        'end_date'      => '2024-08-05',
        'total_days'    => 5,
        'status'        => LeaveRequestStatus::Pending->value,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list leave requests', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/leave-requests')
        ->assertOk();
});

it('unauthenticated request redirects from leave requests list', function () {
    $this->get('/hr/leave-requests')->assertRedirect();
});

it('tenant without permission gets 403 on leave requests list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/leave-requests')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a leave request record directly', function () {
    $request = LeaveRequest::withoutGlobalScopes()->create([
        'company_id'    => $this->company->id,
        'employee_id'   => $this->employee->id,
        'leave_type_id' => $this->leaveType->id,
        'start_date'    => '2024-09-01',
        'end_date'      => '2024-09-03',
        'total_days'    => 3,
        'status'        => LeaveRequestStatus::Pending->value,
    ]);

    expect($request->exists)->toBeTrue();
    expect($request->total_days)->toEqual(3);
    expect($request->status)->toBe(LeaveRequestStatus::Pending);
});

// ---------- Events on LeaveRequested ----------

it('dispatches LeaveRequested event', function () {
    Event::fake();

    LeaveRequested::dispatch($this->leaveRequest);

    Event::assertDispatched(LeaveRequested::class, function ($event) {
        return $event->leaveRequest->id === $this->leaveRequest->id;
    });
});

// ---------- Approve action ----------

it('can approve a leave request and status changes to approved', function () {
    Event::fake();

    $this->leaveRequest->update([
        'status'               => LeaveRequestStatus::Approved->value,
        'approved_by_tenant_id' => $this->tenant->id,
        'approved_at'          => now(),
    ]);

    event(new LeaveApproved($this->leaveRequest->fresh()));

    expect($this->leaveRequest->fresh()->status)->toBe(LeaveRequestStatus::Approved);
    Event::assertDispatched(LeaveApproved::class);
});

// ---------- Reject action ----------

it('can reject a leave request and status changes to rejected', function () {
    Event::fake();

    $this->leaveRequest->update([
        'status'           => LeaveRequestStatus::Rejected->value,
        'rejection_reason' => 'Insufficient notice',
    ]);

    event(new LeaveRejected($this->leaveRequest->fresh()));

    expect($this->leaveRequest->fresh()->status)->toBe(LeaveRequestStatus::Rejected);
    Event::assertDispatched(LeaveRejected::class);
});

// ---------- Delete ----------

it('can soft-delete a leave request', function () {
    $this->leaveRequest->delete();

    expect($this->leaveRequest->trashed())->toBeTrue();
    expect(LeaveRequest::withTrashed()->withoutGlobalScopes()->find($this->leaveRequest->id))->not->toBeNull();
});

it('soft-deleted leave requests do not appear in tenant-scoped list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->leaveRequest->delete();

    expect(LeaveRequest::all()->pluck('id'))->not->toContain($this->leaveRequest->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see leave requests from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.leave-requests.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(LeaveRequest::all()->pluck('id'))->not->toContain($this->leaveRequest->id);
});

// ---------- Casts ----------

it('total_days casts to decimal correctly', function () {
    expect((float) $this->leaveRequest->total_days)->toBe(5.0);
});

it('half day leave request stores correctly', function () {
    $half = LeaveRequest::withoutGlobalScopes()->create([
        'company_id'    => $this->company->id,
        'employee_id'   => $this->employee->id,
        'leave_type_id' => $this->leaveType->id,
        'start_date'    => '2024-10-01',
        'end_date'      => '2024-10-01',
        'total_days'    => 0.5,
        'is_half_day'   => true,
        'status'        => LeaveRequestStatus::Pending->value,
    ]);

    expect($half->is_half_day)->toBeTrue();
    expect((float) $half->total_days)->toBe(0.5);
});
