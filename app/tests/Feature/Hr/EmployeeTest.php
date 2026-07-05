<?php

declare(strict_types=1);

use App\Contracts\Hr\EmployeeServiceInterface;
use App\Data\Hr\CreateEmployeeData;
use App\Data\Hr\OffboardEmployeeData;
use App\Events\Hr\EmployeeHired;
use App\Events\Hr\EmployeeOffboarded;
use App\Exceptions\Hr\ManagerCycleException;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\User;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

function hrCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    return [$company, $owner];
}

test('hiring creates a sequential employee number, fires EmployeeHired, normalises phone', function () {
    Event::fake([EmployeeHired::class]);
    hrCompany();
    $service = app(EmployeeServiceInterface::class);

    $first = $service->hire(new CreateEmployeeData(
        firstName: 'Anna', lastName: 'Jansen', email: 'anna@work.nl',
        jobTitle: 'Engineer', hireDate: now()->toDateString(),
        phone: '06 12345678',
    ));
    $second = $service->hire(new CreateEmployeeData(
        firstName: 'Bram', lastName: 'Visser', email: 'bram@work.nl',
        jobTitle: 'Designer', hireDate: now()->toDateString(),
    ));

    expect($first->employee_number)->toBe('EMP-0001')
        ->and($second->employee_number)->toBe('EMP-0002')
        ->and($first->phone)->toBe('+31612345678');

    Event::assertDispatched(EmployeeHired::class, fn (EmployeeHired $event): bool => $event->employee_id === $first->id && $event->job_title === 'Engineer');
});

test('duplicate work email per company is rejected', function () {
    hrCompany();
    $service = app(EmployeeServiceInterface::class);

    $service->hire(new CreateEmployeeData(
        firstName: 'A', lastName: 'A', email: 'same@work.nl',
        jobTitle: 'X', hireDate: now()->toDateString(),
    ));

    expect(fn () => $service->hire(new CreateEmployeeData(
        firstName: 'B', lastName: 'B', email: 'same@work.nl',
        jobTitle: 'Y', hireDate: now()->toDateString(),
    )))->toThrow(ValidationException::class);
});

test('encrypted fields land as ciphertext with working hash lookup', function () {
    hrCompany();
    $service = app(EmployeeServiceInterface::class);

    $employee = $service->hire(new CreateEmployeeData(
        firstName: 'Secret', lastName: 'Person', email: 'secret@work.nl',
        jobTitle: 'Agent', hireDate: now()->toDateString(),
        nationalId: '123456789', dateOfBirth: '1990-05-15', personalEmail: 'private@home.nl',
    ));

    $raw = DB::table('hr_employees')->where('id', $employee->id)->first();

    expect($raw->national_id)->not->toContain('123456789')
        ->and($raw->date_of_birth)->not->toContain('1990')
        ->and($raw->personal_email)->not->toContain('private@home.nl')
        ->and($employee->fresh()->national_id)->toBe('123456789')
        ->and($employee->birth_year)->toBe(1990)
        ->and(Employee::query()->where('national_id_hash', hash('sha256', '123456789'))->exists())->toBeTrue();
});

test('manager cycles are rejected, valid chains allowed', function () {
    hrCompany();
    $service = app(EmployeeServiceInterface::class);

    $top = $service->hire(new CreateEmployeeData(firstName: 'Top', lastName: 'Boss', email: 't@w.nl', jobTitle: 'CEO', hireDate: now()->toDateString()));
    $mid = $service->hire(new CreateEmployeeData(firstName: 'Mid', lastName: 'Lead', email: 'm@w.nl', jobTitle: 'Lead', hireDate: now()->toDateString(), managerId: $top->id));
    $bottom = $service->hire(new CreateEmployeeData(firstName: 'Low', lastName: 'Dev', email: 'l@w.nl', jobTitle: 'Dev', hireDate: now()->toDateString(), managerId: $mid->id));

    // top reporting to bottom closes the loop
    expect(fn () => $service->changeManager($top->id, $bottom->id))
        ->toThrow(ManagerCycleException::class);

    // self-management is a cycle too
    expect(fn () => $service->changeManager($mid->id, $mid->id))
        ->toThrow(ManagerCycleException::class);

    $service->changeManager($bottom->id, $top->id); // shortcut up the chain is fine
    expect($bottom->fresh()->manager_id)->toBe($top->id);
});

test('offboarding requires a reason, flips the state, and fires EmployeeOffboarded', function () {
    Event::fake([EmployeeOffboarded::class]);
    hrCompany();
    $service = app(EmployeeServiceInterface::class);

    $employee = $service->hire(new CreateEmployeeData(
        firstName: 'Leaving', lastName: 'Soon', email: 'bye@work.nl',
        jobTitle: 'Temp', hireDate: now()->subMonth()->toDateString(),
    ));

    expect(fn () => $service->offboard(new OffboardEmployeeData($employee->id, now()->toDateString(), '  ')))
        ->toThrow(ValidationException::class);

    $service->offboard(new OffboardEmployeeData($employee->id, now()->toDateString(), 'Contract ended'));

    expect((string) $employee->fresh()->status)->toBe('terminated')
        ->and($employee->fresh()->termination_reason)->toBe('Contract ended');

    Event::assertDispatched(EmployeeOffboarded::class, fn (EmployeeOffboarded $event): bool => $event->reason === 'Contract ended');
});

test('tenant isolation: company B sees no company A employees', function () {
    hrCompany();
    app(EmployeeServiceInterface::class)->hire(new CreateEmployeeData(
        firstName: 'Hidden', lastName: 'Person', email: 'hidden@a.nl',
        jobTitle: 'X', hireDate: now()->toDateString(),
    ));

    hrCompany(); // company B

    expect(Employee::query()->count())->toBe(0);
});
