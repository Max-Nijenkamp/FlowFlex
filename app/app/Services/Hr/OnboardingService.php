<?php

declare(strict_types=1);

namespace App\Services\Hr;

use App\Models\Hr\Employee;
use App\Models\Hr\OnboardingPlan;
use App\Models\Hr\OnboardingPlanTask;
use App\Models\Hr\OnboardingTask;
use App\Models\Hr\OnboardingTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Plan generation + task lifecycle (hr.onboarding). The department
 * template wins over the company default; every template task
 * materialises into a plan task; completing/skipping the last task
 * stamps the plan complete.
 */
class OnboardingService
{
    public function generatePlan(Employee $employee): ?OnboardingPlan
    {
        // Idempotent per employee — a re-fired event never doubles the plan.
        $existing = OnboardingPlan::query()->where('employee_id', $employee->id)->first();

        if ($existing instanceof OnboardingPlan) {
            return $existing;
        }

        $template = OnboardingTemplate::query()
            ->where('department_id', $employee->department_id)
            ->first()
            ?? OnboardingTemplate::query()->where('is_default', true)->first();

        if (! $template instanceof OnboardingTemplate) {
            return null; // no templates yet — nothing to generate
        }

        return DB::transaction(function () use ($employee, $template): OnboardingPlan {
            /** @var OnboardingPlan $plan */
            $plan = OnboardingPlan::query()->create([
                'company_id' => $employee->company_id,
                'employee_id' => $employee->id,
                'template_id' => $template->id,
                'started_at' => now(),
            ]);

            /** @var Collection<int, OnboardingTask> $tasks */
            $tasks = $template->tasks()->get();

            foreach ($tasks as $task) {
                OnboardingPlanTask::query()->create([
                    'company_id' => $employee->company_id,
                    'plan_id' => $plan->id,
                    'task_id' => $task->id,
                ]);
            }

            return $plan;
        });
    }

    public function completeTask(OnboardingPlanTask $planTask, bool $skipped = false): void
    {
        $planTask->update([
            'status' => $skipped ? 'skipped' : 'complete',
            'completed_by' => Auth::id(),
            'completed_at' => now(),
        ]);

        $plan = $planTask->plan()->firstOrFail();

        $openCount = $plan->planTasks()->where('status', 'pending')->count();

        if ($openCount === 0 && $plan->completed_at === null) {
            $plan->update(['completed_at' => now()]);
        }
    }
}
