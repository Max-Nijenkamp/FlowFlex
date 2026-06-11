<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\Employee;
use App\Models\HR\HeadcountPlan;
use Illuminate\Support\Collection;

class WorkforceService
{
    /** @return Collection<int, array<string, mixed>> per-plan target vs current active headcount */
    public function planVsActual(string $period): Collection
    {
        $rows = collect();

        foreach (HeadcountPlan::query()->where('period', $period)->get() as $plan) {
            $actual = Employee::query()
                ->where('status', '!=', 'terminated')
                ->when($plan->department_id !== null, fn ($q) => $q->where('department_id', $plan->department_id))
                ->count();

            $rows->push([
                'plan_id' => $plan->id,
                'department_id' => $plan->department_id,
                'target' => $plan->target_headcount,
                'actual' => $actual,
                'gap' => $plan->target_headcount - $actual,
            ]);
        }

        return $rows;
    }
}
