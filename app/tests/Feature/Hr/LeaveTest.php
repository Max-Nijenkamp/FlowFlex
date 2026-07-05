<?php

declare(strict_types=1);

use App\Contracts\Hr\EmployeeServiceInterface;
use App\Data\Hr\CreateEmployeeData;
use App\Data\Hr\SubmitLeaveRequestData;
use App\Events\Hr\LeaveRequestApproved;
use App\Exceptions\Hr\LeaveOverlapException;
use App\Models\Company;
use App\Models\Hr\LeaveBalance;
use App\Models\Hr\LeaveRequest;
use App\Models\Hr\LeaveType;
use App\Models\User;
use App\Services\Hr\LeaveService;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

function leaveCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    $employee = app(EmployeeServiceInterface::class)->hire(new CreateEmployeeData(
        firstName: 'Emma', lastName: 'Vries', email: 'emma@work.nl',
        jobTitle: 'PM', hireDate: now()->subYear()->toDateString(),
    ));

    $type = LeaveType::factory()->create([
        'company_id' => $company->id, 'name' => 'Holiday',
        'accrual_days_per_year' => 25, 'carry_over_days' => 5, 'requires_approval' => true,
    ]);

    return [$company, $owner, $employee, $type];
}

/** Next Monday keeps working-day math deterministic. */
function nextMonday(): Carbon
{
    return now()->next('Monday');
}

test('submit counts working days only, moves pending, and blocks overlaps', function () {
    [, , $employee, $type] = leaveCompany();
    $service = app(LeaveService::class);

    // Mon → next Wed spans 7 weekend-inclusive days = 7 working days minus weekend = Mon..Fri + Mon..Wed? keep simple: Mon..Fri
    $request = $service->submit(new SubmitLeaveRequestData(
        employeeId: $employee->id,
        leaveTypeId: $type->id,
        startDate: nextMonday()->toDateString(),
        endDate: nextMonday()->addDays(6)->toDateString(), // Mon..Sun
    ));

    expect((float) $request->days_requested)->toBe(5.0) // weekend excluded
        ->and((string) $request->status)->toBe('submitted');

    $balance = LeaveBalance::query()->firstOrFail();
    expect((float) $balance->pending_days)->toBe(5.0);

    // Overlapping second request rejected
    expect(fn () => $service->submit(new SubmitLeaveRequestData(
        employeeId: $employee->id,
        leaveTypeId: $type->id,
        startDate: nextMonday()->addDays(2)->toDateString(),
        endDate: nextMonday()->addDays(8)->toDateString(),
    )))->toThrow(LeaveOverlapException::class);
});

test('approval moves pending to taken and fires the event; rejection needs a reason and releases pending', function () {
    Event::fake([LeaveRequestApproved::class]);
    [, , $employee, $type] = leaveCompany();
    $service = app(LeaveService::class);

    $request = $service->submit(new SubmitLeaveRequestData(
        employeeId: $employee->id, leaveTypeId: $type->id,
        startDate: nextMonday()->toDateString(), endDate: nextMonday()->addDays(4)->toDateString(),
    ));

    $service->approve($request);

    $balance = LeaveBalance::query()->firstOrFail();
    expect((float) $balance->pending_days)->toBe(0.0)
        ->and((float) $balance->taken_days)->toBe(5.0);
    Event::assertDispatched(LeaveRequestApproved::class, fn (LeaveRequestApproved $event): bool => $event->days === 5.0);

    // second, non-overlapping request → reject path
    $second = $service->submit(new SubmitLeaveRequestData(
        employeeId: $employee->id, leaveTypeId: $type->id,
        startDate: nextMonday()->addWeeks(4)->toDateString(), endDate: nextMonday()->addWeeks(4)->toDateString(),
    ));

    expect(fn () => $service->reject($second, ''))->toThrow(ValidationException::class);

    $service->reject($second->fresh(), 'Busy sprint');
    expect((float) LeaveBalance::query()->firstOrFail()->pending_days)->toBe(0.0);
});

test('cancelling an approved request returns the taken days', function () {
    [, , $employee, $type] = leaveCompany();
    $service = app(LeaveService::class);

    $request = $service->submit(new SubmitLeaveRequestData(
        employeeId: $employee->id, leaveTypeId: $type->id,
        startDate: nextMonday()->toDateString(), endDate: nextMonday()->addDays(1)->toDateString(),
    ));
    $service->approve($request);
    $service->cancel($request->fresh());

    $balance = LeaveBalance::query()->firstOrFail();
    expect((float) $balance->taken_days)->toBe(0.0)
        ->and((string) $request->fresh()->status)->toBe('cancelled');
});

test('auto-approve types approve on submit', function () {
    [$company, , $employee] = leaveCompany();
    $autoType = LeaveType::factory()->create([
        'company_id' => $company->id, 'name' => 'Sick', 'requires_approval' => false,
    ]);

    $request = app(LeaveService::class)->submit(new SubmitLeaveRequestData(
        employeeId: $employee->id, leaveTypeId: $autoType->id,
        startDate: nextMonday()->toDateString(), endDate: nextMonday()->toDateString(),
    ));

    expect((string) $request->status)->toBe('approved');
});

test('the accrual run allocates yearly days plus capped carry-over and is idempotent', function () {
    [$company, , $employee, $type] = leaveCompany();
    $service = app(LeaveService::class);
    $year = (int) now()->format('Y');

    // Previous year: 25 allocated, 12 taken → remainder 13, capped at 5 carry-over
    LeaveBalance::query()->create([
        'company_id' => $company->id, 'employee_id' => $employee->id,
        'leave_type_id' => $type->id, 'year' => $year - 1,
        'allocated_days' => 25, 'taken_days' => 12,
    ]);

    $service->runAccrual($company->id, $year);
    $service->runAccrual($company->id, $year); // idempotent

    $balance = LeaveBalance::query()->where('year', $year)->firstOrFail();
    expect((float) $balance->allocated_days)->toBe(30.0); // 25 + min(13, 5)
});

test('tenant isolation: leave in company A is invisible to company B', function () {
    [, , $employee, $type] = leaveCompany();
    app(LeaveService::class)->submit(new SubmitLeaveRequestData(
        employeeId: $employee->id, leaveTypeId: $type->id,
        startDate: nextMonday()->toDateString(), endDate: nextMonday()->toDateString(),
    ));

    leaveCompany(); // company B

    expect(LeaveRequest::query()->count())->toBe(0);
});
