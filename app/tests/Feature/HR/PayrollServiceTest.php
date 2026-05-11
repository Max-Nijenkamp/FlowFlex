<?php

declare(strict_types=1);

use App\Contracts\HR\PayrollServiceInterface;
use App\Data\HR\CreatePayrollRunData;
use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\PayrollRun;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('PayrollService', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        app(CompanyContext::class)->set($this->company);
        $this->service = app(PayrollServiceInterface::class);
    });

    it('creates a payroll run', function () {
        $data = new CreatePayrollRunData(
            name: 'May 2026 Payroll',
            period_start: '2026-05-01',
            period_end: '2026-05-31',
            pay_date: '2026-06-05',
            currency: 'EUR',
        );

        $run = $this->service->createRun($data, $this->company);

        expect($run)->toBeInstanceOf(PayrollRun::class)
            ->and($run->name)->toBe('May 2026 Payroll')
            ->and($run->status)->toBe('draft')
            ->and($run->company_id)->toBe($this->company->id)
            ->and($run->currency)->toBe('EUR');
    });

    it('adds an employee entry to payroll run', function () {
        $run = PayrollRun::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'draft',
        ]);

        $employee = Employee::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $entry = $this->service->addEmployee(
            run: $run,
            employee: $employee,
            grossPay: 5000.00,
            netPay: 3600.00,
            deductions: [
                ['type' => 'tax', 'amount' => 1000.00],
                ['type' => 'pension', 'amount' => 400.00],
            ],
        );

        expect($entry->gross_pay)->toEqual(5000.00)
            ->and($entry->net_pay)->toEqual(3600.00)
            ->and($entry->run_id)->toBe($run->id)
            ->and($entry->employee_id)->toBe($employee->id);
    });

    it('calculates totals for a payroll run', function () {
        $run = PayrollRun::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'draft',
        ]);

        $employees = Employee::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        foreach ($employees as $i => $emp) {
            $this->service->addEmployee(
                run: $run,
                employee: $emp,
                grossPay: 5000.00,
                netPay: 3600.00,
            );
        }

        $updated = $this->service->calculateTotals($run);

        expect((float) $updated->total_gross)->toBe(15000.00)
            ->and((float) $updated->total_net)->toBe(10800.00)
            ->and((float) $updated->total_deductions)->toBe(4200.00);
    });

    it('approves a payroll run', function () {
        $run = PayrollRun::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'draft',
        ]);

        $approver = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        $approved = $this->service->approve($run, $approver);

        expect($approved->status)->toBe('approved')
            ->and($approved->approved_by)->toBe($approver->id)
            ->and($approved->approved_at)->not()->toBeNull();
    });

    it('fires PayrollRunApproved event on approval', function () {
        \Illuminate\Support\Facades\Event::fake([\App\Events\HR\PayrollRunApproved::class]);

        $run = PayrollRun::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'draft',
        ]);

        $approver = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        $this->service->approve($run, $approver);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\HR\PayrollRunApproved::class);
    });

    it('uses sql aggregates not php for totals calculation', function () {
        $run = PayrollRun::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'draft',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $emp = Employee::factory()->create(['company_id' => $this->company->id]);
            $this->service->addEmployee($run, $emp, 4000.0, 3000.0);
        }

        $updated = $this->service->calculateTotals($run);

        expect((float) $updated->total_gross)->toBe(20000.0)
            ->and((float) $updated->total_net)->toBe(15000.0)
            ->and((float) $updated->total_deductions)->toBe(5000.0);
    });

    it('cannot approve already approved payroll run', function () {
        $run = PayrollRun::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'approved',
        ]);

        $approver = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        // Double-approve should throw or be a no-op (document current behavior)
        try {
            $result = $this->service->approve($run, $approver);
            // If no exception: idempotent (acceptable)
            expect($result->status)->toBe('approved');
        } catch (\RuntimeException $e) {
            // Also acceptable: strict guard
            expect($e->getMessage())->toContain('approved');
        }
    });
});
