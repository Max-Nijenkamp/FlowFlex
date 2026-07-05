<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\OnboardingPlanResource\Pages;

use App\Filament\Hr\Resources\OnboardingPlanResource;
use App\Models\Hr\OnboardingPlan;
use App\Models\Hr\OnboardingPlanTask;
use App\Models\User;
use App\Services\BillingService;
use App\Services\Hr\OnboardingService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

/**
 * Task checklist for one plan (hr.onboarding/task-checklists).
 * Complete/skip per task; the plan self-completes on the last one.
 */
class ManagePlanTasks extends Page
{
    protected static string $resource = OnboardingPlanResource::class;

    protected string $view = 'filament.hr.pages.manage-plan-tasks';

    public OnboardingPlan $record;

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('hr.onboarding.view-any')
            && app(BillingService::class)->hasModule('hr.onboarding');
    }

    public function getTitle(): string
    {
        return 'Onboarding — '.($this->record->employee()->first()->full_name ?? '');
    }

    public function completeTask(string $planTaskId): void
    {
        $this->decide($planTaskId, skipped: false);
    }

    public function skipTask(string $planTaskId): void
    {
        $this->decide($planTaskId, skipped: true);
    }

    private function decide(string $planTaskId, bool $skipped): void
    {
        $user = Auth::user();

        if (! ($user instanceof User && $user->can('hr.onboarding.manage'))) {
            Notification::make()->danger()->title('You cannot work onboarding tasks.')->send();

            return;
        }

        $planTask = OnboardingPlanTask::query()->findOrFail($planTaskId);
        app(OnboardingService::class)->completeTask($planTask, $skipped);

        Notification::make()->success()->title($skipped ? 'Task skipped' : 'Task completed')->send();
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $tasks = $this->record->planTasks()
            ->with(['task', 'completedBy'])
            ->get()
            ->sortBy(fn (OnboardingPlanTask $planTask): int => $planTask->task()->first()->order ?? 0)
            ->values();

        return [
            'tasks' => $tasks,
            'progress' => $this->record->progressPercent(),
            'startedAt' => $this->record->started_at,
        ];
    }
}
