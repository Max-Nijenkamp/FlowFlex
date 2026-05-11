<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Contracts\HR\OnboardingServiceInterface;
use App\Events\HR\EmployeeOnboardingCompleted;
use App\Events\HR\EmployeeOnboardingStarted;
use App\Models\HR\Employee;
use App\Models\HR\OnboardingChecklist;
use App\Models\HR\OnboardingChecklistItem;
use App\Models\HR\OnboardingTemplate;

class OnboardingService implements OnboardingServiceInterface
{
    public function createChecklist(Employee $employee, OnboardingTemplate $template): OnboardingChecklist
    {
        $company = $employee->company()->withoutGlobalScopes()->first();

        $checklist = OnboardingChecklist::withoutGlobalScopes()->create([
            'company_id'  => $company->id,
            'employee_id' => $employee->id,
            'template_id' => $template->id,
            'start_date'  => $employee->hire_date ?? now()->toDateString(),
        ]);

        // Create items from template tasks — batch insert avoids N individual queries
        $items = $template->tasks()->withoutGlobalScopes()->get();

        $insertData = $items->map(fn ($task) => [
            'id'           => (string) \Illuminate\Support\Str::ulid(),
            'company_id'   => $company->id,
            'checklist_id' => $checklist->id,
            'title'        => $task->title,
            'description'  => $task->description,
            'is_required'  => $task->is_required,
            'sort_order'   => $task->sort_order,
            'due_date'     => ($task->due_days_after_hire && $employee->hire_date)
                ? \Carbon\Carbon::parse($employee->hire_date)->addDays($task->due_days_after_hire)->toDateString()
                : null,
            'created_at'   => now()->toDateTimeString(),
            'updated_at'   => now()->toDateTimeString(),
        ])->toArray();

        if (! empty($insertData)) {
            OnboardingChecklistItem::insert($insertData);
        }

        event(new EmployeeOnboardingStarted($company, $checklist));

        return $checklist;
    }

    public function completeItem(OnboardingChecklistItem $item): OnboardingChecklistItem
    {
        $item->update(['completed_at' => now()]);

        // Check if all required items are complete → complete checklist
        $checklist = $item->checklist()->withoutGlobalScopes()->first();

        if ($checklist && ! $checklist->isCompleted()) {
            $requiredIncomplete = $checklist->items()
                ->withoutGlobalScopes()
                ->where('is_required', true)
                ->whereNull('completed_at')
                ->exists();

            if (! $requiredIncomplete) {
                $checklist->update(['completed_at' => now()]);

                $company = $checklist->company()->withoutGlobalScopes()->first();
                event(new EmployeeOnboardingCompleted($company, $checklist));
            }
        }

        return $item->fresh();
    }

    public function getProgress(OnboardingChecklist $checklist): array
    {
        $items = $checklist->items()->withoutGlobalScopes()->get();

        $total        = $items->count();
        $completed    = $items->whereNotNull('completed_at')->count();
        $reqTotal     = $items->where('is_required', true)->count();
        $reqCompleted = $items->where('is_required', true)->whereNotNull('completed_at')->count();

        return [
            'total'                => $total,
            'completed'            => $completed,
            'percentage'           => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            'required_total'       => $reqTotal,
            'required_completed'   => $reqCompleted,
            'required_percentage'  => $reqTotal > 0 ? (int) round(($reqCompleted / $reqTotal) * 100) : 0,
            'is_complete'          => $checklist->isCompleted(),
        ];
    }
}
