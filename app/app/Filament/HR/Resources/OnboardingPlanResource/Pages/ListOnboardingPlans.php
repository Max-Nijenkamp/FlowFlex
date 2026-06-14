<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\OnboardingPlanResource\Pages;

use App\Contracts\HR\OnboardingServiceInterface;
use App\Filament\HR\Resources\OnboardingPlanResource;
use App\Models\HR\Employee;
use App\Models\HR\OnboardingPlan;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingPlans extends ListRecords
{
    protected static string $resource = OnboardingPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Start onboarding')
                // Route through the service so template tasks are copied onto the plan.
                ->using(function (array $data): OnboardingPlan {
                    $employee = Employee::query()->findOrFail((string) $data['employee_id']);

                    $plan = app(OnboardingServiceInterface::class)->startPlan(
                        $employee->company_id,
                        $employee->id,
                        (string) $data['template_id'],
                    );

                    // Contract: null only when no template resolves — fall back to a bare plan.
                    $plan ??= OnboardingPlan::query()->create([
                        'employee_id' => $employee->id,
                        'template_id' => (string) $data['template_id'],
                        'started_at' => $data['started_at'],
                    ]);

                    if (isset($data['started_at'])) {
                        $plan->update(['started_at' => $data['started_at']]);
                    }

                    return $plan;
                }),
        ];
    }
}
