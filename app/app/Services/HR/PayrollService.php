<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Contracts\HR\PayrollServiceInterface;
use App\Data\HR\CreatePayrollRunData;
use App\Events\HR\PayrollRunApproved;
use App\Exceptions\HR\CannotApproveOwnRunException;
use App\Exceptions\HR\IncompletePayrollProfileException;
use App\Models\HR\DeductionType;
use App\Models\HR\PayrollEmployee;
use App\Models\HR\PayrollRun;
use App\Models\HR\Payslip;
use App\States\HR\PayrollRun\Approved;
use App\States\HR\PayrollRun\Processing;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollService implements PayrollServiceInterface
{
    public function createRun(CreatePayrollRunData $data): PayrollRun
    {
        $exists = PayrollRun::query()
            ->whereDate('period_start', $data->period_start)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'period_start' => 'A payroll run for this period already exists.',
            ]);
        }

        $run = PayrollRun::create([
            'period_start' => $data->period_start,
            'period_end' => $data->period_end,
            'created_by' => Auth::guard('web')->id(),
        ]);

        // Stash the selected employees as pending payslip rows at process time.
        cache()->put("payroll-run:{$run->id}:employees", $data->employee_ids, now()->addDay());

        return $run;
    }

    public function processRun(string $runId): PayrollRun
    {
        $run = PayrollRun::query()->findOrFail($runId);
        /** @var list<string> $employeeIds */
        $employeeIds = cache()->get("payroll-run:{$run->id}:employees", []);

        $profiles = PayrollEmployee::query()->whereIn('employee_id', $employeeIds)->get();

        $incomplete = $profiles->where('status', '!=', 'ready')->pluck('employee_id');
        if ($incomplete->isNotEmpty()) {
            throw new IncompletePayrollProfileException(
                'Payroll profiles incomplete for employees: '.$incomplete->implode(', ')
            );
        }

        $run->status->transitionTo(Processing::class);

        DB::transaction(function () use ($run, $profiles): void {
            $totalGross = Money::ofMinor(0, $run->currency);
            $totalNet = Money::ofMinor(0, $run->currency);
            $totalEmployer = Money::ofMinor(0, $run->currency);
            $deductionTypes = DeductionType::query()->get();

            foreach ($profiles as $profile) {
                $gross = Money::ofMinor($profile->salaryCents() ?? 0, $run->currency);
                $net = $gross;
                $employerCost = $gross;
                $deductions = [];

                foreach ($deductionTypes as $type) {
                    $amount = $type->calculation_type === 'percent'
                        ? $gross->multipliedBy($type->value)->dividedBy(10_000, RoundingMode::HALF_UP)
                        : Money::ofMinor($type->value, $run->currency);

                    if ($type->is_employer_contribution) {
                        $employerCost = $employerCost->plus($amount);
                    } else {
                        $net = $net->minus($amount);
                    }

                    $deductions[] = ['name' => $type->name, 'amount_cents' => $amount->getMinorAmount()->toInt()];
                }

                Payslip::query()->updateOrCreate(
                    ['payroll_run_id' => $run->id, 'employee_id' => $profile->employee_id],
                    [
                        'company_id' => $run->company_id,
                        'amounts_raw' => json_encode([
                            'gross_cents' => $gross->getMinorAmount()->toInt(),
                            'net_cents' => $net->getMinorAmount()->toInt(),
                            'employer_cost_cents' => $employerCost->getMinorAmount()->toInt(),
                            'deductions' => $deductions,
                        ], JSON_THROW_ON_ERROR),
                    ],
                );

                $totalGross = $totalGross->plus($gross);
                $totalNet = $totalNet->plus($net);
                $totalEmployer = $totalEmployer->plus($employerCost);
            }

            $run->forceFill([
                'total_gross_cents' => $totalGross->getMinorAmount()->toInt(),
                'total_net_cents' => $totalNet->getMinorAmount()->toInt(),
                'total_employer_cost_cents' => $totalEmployer->getMinorAmount()->toInt(),
            ])->save();
        });

        return $run->refresh();
    }

    public function approveRun(string $runId): PayrollRun
    {
        $run = PayrollRun::query()->findOrFail($runId);

        // Four-eyes: the creator may not approve their own run.
        if ($run->created_by !== null && $run->created_by === Auth::guard('web')->id()) {
            throw new CannotApproveOwnRunException('Payroll runs require a second approver.');
        }

        $run->status->transitionTo(Approved::class);
        $run->forceFill(['approved_by' => Auth::guard('web')->id(), 'approved_at' => now()])->save();

        event(new PayrollRunApproved(
            company_id: $run->company_id,
            payroll_run_id: $run->id,
            period_start: CarbonImmutable::parse($run->period_start),
            period_end: CarbonImmutable::parse($run->period_end),
            total_gross_cents: $run->total_gross_cents,
            total_net_cents: $run->total_net_cents,
            currency: $run->currency,
        ));

        return $run->refresh();
    }
}
