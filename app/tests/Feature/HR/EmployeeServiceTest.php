<?php

declare(strict_types=1);

use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Data\HR\UpdateEmployeeData;
use App\Models\Company;
use App\Models\HR\Employee;
use App\Support\Services\CompanyContext;

describe('EmployeeService', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        app(CompanyContext::class)->set($this->company);
        $this->service = app(EmployeeServiceInterface::class);
    });

    it('creates an employee', function () {
        $data = new CreateEmployeeData(
            employee_number: 'EMP-001',
            first_name: 'Jane',
            last_name: 'Doe',
            email: 'jane@example.com',
            hire_date: '2026-01-01',
            employment_type: 'full_time',
        );

        $employee = $this->service->create($data, $this->company);

        expect($employee)->toBeInstanceOf(Employee::class)
            ->and($employee->first_name)->toBe('Jane')
            ->and($employee->last_name)->toBe('Doe')
            ->and($employee->employee_number)->toBe('EMP-001')
            ->and($employee->company_id)->toBe($this->company->id)
            ->and($employee->status)->toBe('active');
    });

    it('fires EmployeeHired event when creating employee', function () {
        \Illuminate\Support\Facades\Event::fake([\App\Events\HR\EmployeeHired::class]);

        $data = new CreateEmployeeData(
            employee_number: 'EMP-002',
            first_name: 'John',
            last_name: 'Smith',
            email: 'john@example.com',
            hire_date: '2026-01-01',
            employment_type: 'full_time',
        );

        $this->service->create($data, $this->company);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\HR\EmployeeHired::class);
    });

    it('updates an employee', function () {
        $employee = Employee::factory()->create([
            'company_id' => $this->company->id,
            'first_name' => 'Jane',
        ]);

        $update = new UpdateEmployeeData(first_name: 'Janet', job_title: 'Manager');
        $updated = $this->service->update($employee, $update);

        expect($updated->first_name)->toBe('Janet')
            ->and($updated->job_title)->toBe('Manager');
    });

    it('terminates an employee', function () {
        $employee = Employee::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        $terminated = $this->service->terminate($employee, '2026-05-01');

        expect($terminated->status)->toBe('terminated')
            ->and($terminated->termination_date->format('Y-m-d'))->toBe('2026-05-01');
    });

    it('fires EmployeeTerminated event when terminating', function () {
        \Illuminate\Support\Facades\Event::fake([\App\Events\HR\EmployeeTerminated::class]);

        $employee = Employee::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        $this->service->terminate($employee);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\HR\EmployeeTerminated::class);
    });

    it('factory creates employee with company scope', function () {
        $employee = Employee::factory()->create(['company_id' => $this->company->id]);

        expect($employee->company_id)->toBe($this->company->id);
    });

    it('company scope filters employees from other companies', function () {
        $otherCompany = Company::factory()->create(['status' => 'active']);

        Employee::factory()->create(['company_id' => $this->company->id]);
        Employee::factory()->create(['company_id' => $otherCompany->id]);

        // Scope is active for $this->company
        $employees = Employee::all();
        expect($employees->count())->toBe(1)
            ->and($employees->first()->company_id)->toBe($this->company->id);
    });

    it('can assign and clear manager via update', function () {
        $manager = Employee::factory()->create(['company_id' => $this->company->id]);
        $employee = Employee::factory()->create([
            'company_id' => $this->company->id,
            'manager_id' => null,
        ]);

        // Assign manager
        $update = new UpdateEmployeeData(manager_id: $manager->id);
        $updated = $this->service->update($employee, $update);
        expect($updated->manager_id)->toBe($manager->id);

        // Clear manager (set to null explicitly)
        $update2 = new UpdateEmployeeData(manager_id: null);
        $cleared = $this->service->update($updated, $update2);
        expect($cleared->manager_id)->toBeNull();
    });
});
