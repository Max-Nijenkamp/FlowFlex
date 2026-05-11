<?php

declare(strict_types=1);

use App\Filament\Projects\Resources\KanbanBoardResource;
use App\Filament\Projects\Resources\ProjectMilestoneResource;
use App\Filament\Projects\Resources\ProjectResource;
use App\Filament\Projects\Resources\SprintResource;
use App\Filament\Projects\Resources\TaskResource;
use App\Filament\Projects\Resources\TimeEntryResource;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Core\BillingSubscription;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('Projects Resource Access Control', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
    });

    it('canAccess returns false without subscription for project resource', function () {
        $this->actingAs($this->user);

        expect(ProjectResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without subscription for task resource', function () {
        $this->actingAs($this->user);

        expect(TaskResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without subscription for kanban board resource', function () {
        $this->actingAs($this->user);

        expect(KanbanBoardResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without subscription for sprint resource', function () {
        $this->actingAs($this->user);

        expect(SprintResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without subscription for time entry resource', function () {
        $this->actingAs($this->user);

        expect(TimeEntryResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without subscription for milestone resource', function () {
        $this->actingAs($this->user);

        expect(ProjectMilestoneResource::canAccess())->toBeFalse();
    });

    it('canAccess returns true for projects.tasks with active subscription and billing', function () {
        $this->actingAs($this->user);

        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'projects.tasks',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        expect(TaskResource::canAccess())->toBeTrue();
    });

    it('canAccess returns true for projects.tasks with active subscription and billing for project resource', function () {
        $this->actingAs($this->user);

        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'projects.tasks',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        expect(ProjectResource::canAccess())->toBeTrue();
    });

    it('canAccess returns true for projects.milestones with active subscription and billing', function () {
        $this->actingAs($this->user);

        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'projects.milestones',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        expect(ProjectMilestoneResource::canAccess())->toBeTrue();
    });

    it('canAccess returns true for projects.kanban with active subscription and billing', function () {
        $this->actingAs($this->user);

        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'projects.kanban',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        expect(KanbanBoardResource::canAccess())->toBeTrue();
    });

    it('canAccess returns true for projects.sprints with active subscription and billing', function () {
        $this->actingAs($this->user);

        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'projects.sprints',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        expect(SprintResource::canAccess())->toBeTrue();
    });

    it('canAccess returns true for projects.time with active subscription and billing', function () {
        $this->actingAs($this->user);

        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'projects.time',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        expect(TimeEntryResource::canAccess())->toBeTrue();
    });

    it('canAccess returns false without authentication', function () {
        expect(ProjectResource::canAccess())->toBeFalse();
        expect(TaskResource::canAccess())->toBeFalse();
        expect(KanbanBoardResource::canAccess())->toBeFalse();
        expect(SprintResource::canAccess())->toBeFalse();
        expect(TimeEntryResource::canAccess())->toBeFalse();
        expect(ProjectMilestoneResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without company context', function () {
        app(CompanyContext::class)->clear();
        $this->actingAs($this->user);

        expect(ProjectResource::canAccess())->toBeFalse();
        expect(TaskResource::canAccess())->toBeFalse();
        expect(KanbanBoardResource::canAccess())->toBeFalse();
        expect(SprintResource::canAccess())->toBeFalse();
        expect(TimeEntryResource::canAccess())->toBeFalse();
        expect(ProjectMilestoneResource::canAccess())->toBeFalse();
    });
});

describe('Projects Panel pages load for authenticated user', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
    });

    it('projects dashboard loads for authenticated user', function () {
        $this->actingAs($this->user)
            ->get('/projects')
            ->assertOk();
    });

    it('projects login page is accessible', function () {
        $this->get('/projects/login')->assertOk();
    });
});
