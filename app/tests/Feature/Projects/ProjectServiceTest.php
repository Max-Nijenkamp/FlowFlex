<?php

declare(strict_types=1);

use App\Contracts\Projects\ProjectServiceInterface;
use App\Data\Projects\CreateProjectData;
use App\Models\Company;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectMember;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('Project Service', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create(['company_id' => $this->company->id]);
        app(CompanyContext::class)->set($this->company);
        $this->service = app(ProjectServiceInterface::class);
    });

    it('creates a project', function () {
        $data = new CreateProjectData(
            name: 'Test Project',
            owner_id: $this->user->id,
            description: 'A test project',
        );

        $project = $this->service->create($data);

        expect($project)->toBeInstanceOf(Project::class);
        expect($project->name)->toBe('Test Project');
        expect($project->owner_id)->toBe($this->user->id);
        expect($project->company_id)->toBe($this->company->id);
        expect($project->status)->toBe('planning');
    });

    it('auto-adds owner as project member with owner role', function () {
        $data = new CreateProjectData(
            name: 'Test Project',
            owner_id: $this->user->id,
        );

        $project = $this->service->create($data);

        $member = ProjectMember::withoutGlobalScopes()
            ->where('project_id', $project->id)
            ->where('user_id', $this->user->id)
            ->first();

        expect($member)->not->toBeNull();
        expect($member->role)->toBe('owner');
    });

    it('adds a member to a project', function () {
        $project = Project::factory()->forCompany($this->company)->create(['owner_id' => $this->user->id]);
        $member  = User::factory()->create(['company_id' => $this->company->id]);

        $this->service->addMember($project, $member, 'member');

        $projectMember = ProjectMember::withoutGlobalScopes()
            ->where('project_id', $project->id)
            ->where('user_id', $member->id)
            ->first();

        expect($projectMember)->not->toBeNull();
        expect($projectMember->role)->toBe('member');
    });

    it('enforces company scope isolation', function () {
        $otherCompany = Company::factory()->create(['status' => 'active']);
        $otherUser    = User::factory()->create(['company_id' => $otherCompany->id]);

        // Create project for other company (use withoutGlobalScopes to bypass scope)
        $otherProject = Project::withoutGlobalScopes()->create([
            'company_id' => $otherCompany->id,
            'name'       => 'Other Company Project',
            'owner_id'   => $otherUser->id,
            'status'     => 'planning',
            'priority'   => 'medium',
        ]);

        // Current company can only see its own projects
        $visibleProjects = Project::all();
        expect($visibleProjects->pluck('id'))->not->toContain($otherProject->id);
    });

    it('archives a project', function () {
        $project = Project::factory()->forCompany($this->company)->create(['owner_id' => $this->user->id]);

        $this->service->archive($project);

        expect($project->fresh()->status)->toBe('archived');
    });

    it('archives a project with archived status not cancelled', function () {
        $company = \App\Models\Company::factory()->create(['status' => 'active']);
        app(\App\Support\Services\CompanyContext::class)->set($company);

        $user = \App\Models\User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);

        $project = \App\Models\Projects\Project::factory()->create([
            'company_id' => $company->id,
            'owner_id'   => $user->id,
            'status'     => 'active',
        ]);

        $archived = $this->service->archive($project);

        expect($archived->status)->toBe('archived');
    });
});
