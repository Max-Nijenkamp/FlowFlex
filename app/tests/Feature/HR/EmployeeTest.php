<?php

declare(strict_types=1);

use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Data\HR\OffboardEmployeeData;
use App\Events\HR\EmployeeHired;
use App\Events\HR\EmployeeOffboarded;
use App\Exceptions\HR\ManagerCycleException;
use App\Models\Company;
use App\Models\HR\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->service = app(EmployeeServiceInterface::class);
});

function hireData(array $overrides = []): CreateEmployeeData
{
    return CreateEmployeeData::from(array_merge([
        'first_name' => 'Eva',
        'last_name' => 'Jansen',
        'email' => 'eva@acme.test',
        'hire_date' => '2026-06-01',
        'job_title' => 'Engineer',
        'employment_type' => 'full-time',
    ], $overrides));
}

it('hires an employee with a sequential number and fires EmployeeHired', function () {
    Event::fake([EmployeeHired::class]);

    $first = $this->service->hire(hireData());
    $second = $this->service->hire(hireData(['email' => 'two@acme.test']));

    expect($first->employee_number)->toBe('1')
        ->and($second->employee_number)->toBe('2')
        ->and((string) $first->status)->toBe('active');

    Event::assertDispatched(EmployeeHired::class, fn ($e) => $e->company_id === $this->company->id
        && $e->employee_id === $first->id
        && $e->job_title === 'Engineer');
});

it('keeps employee numbers per company', function () {
    $this->service->hire(hireData());

    $other = Company::factory()->create();
    $this->setCompany($other);

    $employee = $this->service->hire(hireData(['email' => 'other@acme.test']));
    expect($employee->employee_number)->toBe('1');
});

it('rejects duplicate work email within the company', function () {
    $this->service->hire(hireData());

    $this->service->hire(hireData());
})->throws(ValidationException::class);

it('offboards: terminated state + EmployeeOffboarded + required fields', function () {
    Event::fake([EmployeeOffboarded::class]);
    $employee = $this->service->hire(hireData());

    $result = $this->service->offboard(new OffboardEmployeeData(
        employee_id: $employee->id,
        termination_date: '2026-07-01',
        termination_reason: 'Resigned',
    ));

    expect((string) $result->status)->toBe('terminated')
        ->and($result->termination_reason)->toBe('Resigned');

    Event::assertDispatched(EmployeeOffboarded::class, fn ($e) => $e->employee_id === $employee->id);
});

it('rejects manager cycles', function () {
    $a = $this->service->hire(hireData(['email' => 'a@acme.test']));
    $b = $this->service->hire(hireData(['email' => 'b@acme.test']));
    $this->service->assignManager($b->id, $a->id);

    $this->service->assignManager($a->id, $b->id);
})->throws(ManagerCycleException::class);

it('stores national_id encrypted with a deterministic hash for lookup', function () {
    $employee = $this->service->hire(hireData(['national_id' => 'NL-123456789']));

    $raw = DB::table('hr_employees')->where('id', $employee->id)->value('national_id');
    expect($raw)->not->toContain('NL-123456789')
        ->and($employee->national_id_hash)->toBe(hash('sha256', 'NL-123456789'))
        ->and($employee->fresh()->national_id)->toBe('NL-123456789'); // decrypts

    // Hash lookup works without decrypting.
    expect(Employee::query()->where('national_id_hash', hash('sha256', 'NL-123456789'))->exists())->toBeTrue();
});

it('isolates employees between companies', function () {
    $this->service->hire(hireData());

    $this->setCompany(Company::factory()->create());
    expect(Employee::count())->toBe(0);
});
