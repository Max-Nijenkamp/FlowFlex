<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Core\BillingSubscription;
use App\Models\Projects\KanbanBoard;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectMilestone;
use App\Models\Projects\Sprint;
use App\Models\Projects\Task;
use App\Models\User;
use App\Support\Services\CompanyContext;

// ---------------------------------------------------------------------------
// Helpers (scoped to this file via named helpers)
// ---------------------------------------------------------------------------

function activateProjectsBilling(Company $company): void
{
    BillingSubscription::create([
        'company_id' => $company->id,
        'status'     => 'active',
    ]);
}

function activateProjectsModule(Company $company, string $moduleKey): void
{
    CompanyModuleSubscription::withoutGlobalScopes()->create([
        'company_id'   => $company->id,
        'module_key'   => $moduleKey,
        'status'       => 'active',
        'activated_at' => now(),
    ]);
}

// ---------------------------------------------------------------------------
// ProjectResource — module: projects.tasks
// ---------------------------------------------------------------------------

describe('Projects Project Resource', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        activateProjectsBilling($this->company);
        activateProjectsModule($this->company, 'projects.tasks');
    });

    it('list page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/projects/projects')
            ->assertOk();
    });

    it('create page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/projects/projects/create')
            ->assertOk();
    });

    it('list page shows an existing project name', function () {
        Project::factory()->create([
            'company_id' => $this->company->id,
            'owner_id'   => $this->user->id,
            'name'       => 'Alpha Launch Project',
        ]);

        $this->actingAs($this->user)
            ->get('/projects/projects')
            ->assertOk()
            ->assertSee('Alpha Launch Project');
    });

    it('edit page loads for an existing project', function () {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'owner_id'   => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get("/projects/projects/{$project->id}/edit")
            ->assertOk();
    });

    it('list page returns 403 without subscription', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);
        activateProjectsBilling($company);

        $this->actingAs($user)
            ->get('/projects/projects')
            ->assertForbidden();
    });

    it('projects from another company are not visible on list page', function () {
        $otherCompany = Company::factory()->create(['status' => 'active']);
        $otherUser    = User::factory()->create(['company_id' => $otherCompany->id]);

        Project::factory()->create([
            'company_id' => $otherCompany->id,
            'owner_id'   => $otherUser->id,
            'name'       => 'PrivateCorpProject',
        ]);

        $this->actingAs($this->user)
            ->get('/projects/projects')
            ->assertOk()
            ->assertDontSee('PrivateCorpProject');
    });
});

// ---------------------------------------------------------------------------
// TaskResource — module: projects.tasks
// ---------------------------------------------------------------------------

describe('Projects Task Resource', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        activateProjectsBilling($this->company);
        activateProjectsModule($this->company, 'projects.tasks');
    });

    it('list page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/projects/tasks')
            ->assertOk();
    });

    it('list page shows an existing task title', function () {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'owner_id'   => $this->user->id,
        ]);

        Task::factory()->create([
            'company_id'  => $this->company->id,
            'project_id'  => $project->id,
            'created_by'  => $this->user->id,
            'title'       => 'Write unit tests',
        ]);

        $this->actingAs($this->user)
            ->get('/projects/tasks')
            ->assertOk()
            ->assertSee('Write unit tests');
    });

    it('edit page loads for an existing task', function () {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'owner_id'   => $this->user->id,
        ]);

        $task = Task::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get("/projects/tasks/{$task->id}/edit")
            ->assertOk();
    });

    it('list page returns 403 without subscription', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);
        activateProjectsBilling($company);

        $this->actingAs($user)
            ->get('/projects/tasks')
            ->assertForbidden();
    });
});

// ---------------------------------------------------------------------------
// KanbanBoardResource — module: projects.kanban
// ---------------------------------------------------------------------------

describe('Projects Kanban Board Resource', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        activateProjectsBilling($this->company);
        activateProjectsModule($this->company, 'projects.kanban');
    });

    it('list page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/projects/kanban-boards')
            ->assertOk();
    });

    it('list page shows an existing kanban board', function () {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'owner_id'   => $this->user->id,
        ]);

        KanbanBoard::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'name'       => 'Development Board',
        ]);

        $this->actingAs($this->user)
            ->get('/projects/kanban-boards')
            ->assertOk()
            ->assertSee('Development Board');
    });

    it('list page returns 403 without subscription', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);
        activateProjectsBilling($company);

        $this->actingAs($user)
            ->get('/projects/kanban-boards')
            ->assertForbidden();
    });
});

// ---------------------------------------------------------------------------
// SprintResource — module: projects.sprints
// ---------------------------------------------------------------------------

describe('Projects Sprint Resource', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        activateProjectsBilling($this->company);
        activateProjectsModule($this->company, 'projects.sprints');
    });

    it('list page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/projects/sprints')
            ->assertOk();
    });

    it('view page loads for an existing sprint', function () {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'owner_id'   => $this->user->id,
        ]);

        $sprint = Sprint::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
        ]);

        $this->actingAs($this->user)
            ->get("/projects/sprints/{$sprint->id}")
            ->assertOk();
    });

    it('list page shows an existing sprint name', function () {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'owner_id'   => $this->user->id,
        ]);

        Sprint::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'name'       => 'Sprint 7',
        ]);

        $this->actingAs($this->user)
            ->get('/projects/sprints')
            ->assertOk()
            ->assertSee('Sprint 7');
    });

    it('list page returns 403 without subscription', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);
        activateProjectsBilling($company);

        $this->actingAs($user)
            ->get('/projects/sprints')
            ->assertForbidden();
    });
});

// ---------------------------------------------------------------------------
// ProjectMilestoneResource — module: projects.milestones
// ---------------------------------------------------------------------------

describe('Projects Milestone Resource', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        activateProjectsBilling($this->company);
        activateProjectsModule($this->company, 'projects.milestones');
    });

    it('list page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/projects/project-milestones')
            ->assertOk();
    });

    it('list page shows an existing milestone name', function () {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'owner_id'   => $this->user->id,
        ]);

        ProjectMilestone::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'name'       => 'Beta Release',
        ]);

        $this->actingAs($this->user)
            ->get('/projects/project-milestones')
            ->assertOk()
            ->assertSee('Beta Release');
    });

    it('edit page loads for an existing milestone', function () {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'owner_id'   => $this->user->id,
        ]);

        $milestone = ProjectMilestone::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
        ]);

        $this->actingAs($this->user)
            ->get("/projects/project-milestones/{$milestone->id}/edit")
            ->assertOk();
    });

    it('list page returns 403 without subscription', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);
        activateProjectsBilling($company);

        $this->actingAs($user)
            ->get('/projects/project-milestones')
            ->assertForbidden();
    });
});
