<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Contracts\HR\OnboardingServiceInterface;
use App\Models\HR\Employee;
use App\Models\HR\OnboardingPlan;
use App\Models\HR\OnboardingPlanTask;
use App\Models\HR\OnboardingTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OnboardingService implements OnboardingServiceInterface
{
    public function startPlan(string $companyId, string $employeeId, ?string $templateId = null): ?OnboardingPlan
    {
        $employee = Employee::query()->withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->findOrFail($employeeId);

        // Pick: explicit template → department template → company default → no-op.
        $template = $templateId !== null
            ? OnboardingTemplate::query()->withoutGlobalScopes()->where('company_id', $companyId)->find($templateId)
            : OnboardingTemplate::query()->withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->where(function ($q) use ($employee): void {
                    $q->where('department_id', $employee->department_id)->orWhere('is_default', true);
                })
                ->orderByRaw('CASE WHEN department_id IS NOT NULL THEN 0 ELSE 1 END')
                ->first();

        if ($template === null) {
            return null; // no template configured — contract says no-op
        }

        return DB::transaction(function () use ($companyId, $employeeId, $template): OnboardingPlan {
            $plan = OnboardingPlan::query()->withoutGlobalScopes()->create([
                'company_id' => $companyId,
                'employee_id' => $employeeId,
                'template_id' => $template->id,
                'started_at' => now(),
            ]);

            foreach ($template->tasks()->withoutGlobalScopes()->where('template_id', $template->id)->get() as $task) {
                OnboardingPlanTask::query()->withoutGlobalScopes()->create([
                    'plan_id' => $plan->id,
                    'task_id' => $task->id,
                    'company_id' => $companyId,
                ]);
            }

            return $plan;
        });
    }

    public function completeTask(string $planTaskId, string $status = 'complete'): void
    {
        $planTask = OnboardingPlanTask::query()->findOrFail($planTaskId);

        $planTask->update([
            'status' => $status,
            'completed_by' => Auth::guard('web')->id(),
            'completed_at' => now(),
        ]);

        // Auto-complete the plan when no open tasks remain.
        $openRemaining = OnboardingPlanTask::query()
            ->where('plan_id', $planTask->plan_id)
            ->where('status', 'pending')
            ->exists();

        if (! $openRemaining) {
            OnboardingPlan::query()->whereKey($planTask->plan_id)->update(['completed_at' => now()]);
        }
    }

    public function progress(string $planId): float
    {
        $total = OnboardingPlanTask::query()->where('plan_id', $planId)->count();

        if ($total === 0) {
            return 1.0;
        }

        $closed = OnboardingPlanTask::query()->where('plan_id', $planId)->where('status', '!=', 'pending')->count();

        return round($closed / $total, 2);
    }
}
