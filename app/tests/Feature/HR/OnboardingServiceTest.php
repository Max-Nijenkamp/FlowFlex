<?php

declare(strict_types=1);

use App\Contracts\HR\OnboardingServiceInterface;
use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\OnboardingChecklist;
use App\Models\HR\OnboardingTemplate;
use App\Models\HR\OnboardingTemplateTask;
use App\Support\Services\CompanyContext;

describe('OnboardingService', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        app(CompanyContext::class)->set($this->company);
        $this->service = app(OnboardingServiceInterface::class);

        $this->employee = Employee::factory()->create([
            'company_id' => $this->company->id,
            'hire_date'  => '2026-05-10',
            'status'     => 'active',
        ]);

        $this->template = OnboardingTemplate::factory()->create([
            'company_id' => $this->company->id,
            'name'       => 'Standard Onboarding',
        ]);
    });

    it('creates a checklist from template', function () {
        OnboardingTemplateTask::withoutGlobalScopes()->create([
            'company_id'         => $this->company->id,
            'template_id'        => $this->template->id,
            'title'              => 'Setup laptop',
            'due_days_after_hire' => 1,
            'is_required'        => true,
            'sort_order'         => 0,
        ]);

        OnboardingTemplateTask::withoutGlobalScopes()->create([
            'company_id'         => $this->company->id,
            'template_id'        => $this->template->id,
            'title'              => 'HR paperwork',
            'due_days_after_hire' => 3,
            'is_required'        => true,
            'sort_order'         => 1,
        ]);

        $checklist = $this->service->createChecklist($this->employee, $this->template);

        expect($checklist)->toBeInstanceOf(OnboardingChecklist::class)
            ->and($checklist->employee_id)->toBe($this->employee->id)
            ->and($checklist->template_id)->toBe($this->template->id);

        $items = $checklist->items()->withoutGlobalScopes()->get();
        expect($items->count())->toBe(2)
            ->and($items->first()->title)->toBe('Setup laptop');
    });

    it('fires EmployeeOnboardingStarted event', function () {
        \Illuminate\Support\Facades\Event::fake([\App\Events\HR\EmployeeOnboardingStarted::class]);

        $this->service->createChecklist($this->employee, $this->template);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\HR\EmployeeOnboardingStarted::class);
    });

    it('completes a checklist item', function () {
        $checklist = OnboardingChecklist::withoutGlobalScopes()->create([
            'company_id'  => $this->company->id,
            'employee_id' => $this->employee->id,
            'template_id' => $this->template->id,
            'start_date'  => '2026-05-10',
        ]);

        $item = \App\Models\HR\OnboardingChecklistItem::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'checklist_id' => $checklist->id,
            'title'        => 'Setup laptop',
            'is_required'  => true,
            'sort_order'   => 0,
        ]);

        $completedItem = $this->service->completeItem($item);

        expect($completedItem->completed_at)->not()->toBeNull()
            ->and($completedItem->isCompleted())->toBeTrue();
    });

    it('fires EmployeeOnboardingCompleted when all required items done', function () {
        \Illuminate\Support\Facades\Event::fake([\App\Events\HR\EmployeeOnboardingCompleted::class]);

        $checklist = OnboardingChecklist::withoutGlobalScopes()->create([
            'company_id'  => $this->company->id,
            'employee_id' => $this->employee->id,
            'start_date'  => '2026-05-10',
        ]);

        $item = \App\Models\HR\OnboardingChecklistItem::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'checklist_id' => $checklist->id,
            'title'        => 'Only task',
            'is_required'  => true,
            'sort_order'   => 0,
        ]);

        $this->service->completeItem($item);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\HR\EmployeeOnboardingCompleted::class);
    });

    it('returns progress percentages', function () {
        $checklist = OnboardingChecklist::withoutGlobalScopes()->create([
            'company_id'  => $this->company->id,
            'employee_id' => $this->employee->id,
            'start_date'  => '2026-05-10',
        ]);

        \App\Models\HR\OnboardingChecklistItem::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'checklist_id' => $checklist->id,
            'title'        => 'Task 1',
            'is_required'  => true,
            'sort_order'   => 0,
            'completed_at' => now(),
        ]);

        \App\Models\HR\OnboardingChecklistItem::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'checklist_id' => $checklist->id,
            'title'        => 'Task 2',
            'is_required'  => true,
            'sort_order'   => 1,
        ]);

        $progress = $this->service->getProgress($checklist);

        expect($progress['total'])->toBe(2)
            ->and($progress['completed'])->toBe(1)
            ->and($progress['percentage'])->toBe(50)
            ->and($progress['required_percentage'])->toBe(50);
    });
});
