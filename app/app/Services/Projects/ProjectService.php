<?php

declare(strict_types=1);

namespace App\Services\Projects;

use App\Contracts\Projects\ProjectServiceInterface;
use App\Data\Projects\CreateProjectData;
use App\Events\Projects\ProjectCreated;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectMember;
use App\Models\User;

class ProjectService implements ProjectServiceInterface
{
    public function create(CreateProjectData $data): Project
    {
        $project = Project::create([
            'name'        => $data->name,
            'description' => $data->description,
            'owner_id'    => $data->owner_id,
            'priority'    => $data->priority,
            'status'      => $data->status,
            'start_date'  => $data->start_date,
            'due_date'    => $data->due_date,
            'budget'      => $data->budget,
            'color'       => $data->color,
        ]);

        // Auto-add owner as project member with owner role
        ProjectMember::create([
            'project_id' => $project->id,
            'user_id'    => $data->owner_id,
            'role'       => 'owner',
        ]);

        event(new ProjectCreated($project->company, $project));

        return $project;
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project->fresh();
    }

    public function archive(Project $project): Project
    {
        $project->update(['status' => 'archived']);

        return $project->fresh();
    }

    public function addMember(Project $project, User $user, string $role = 'member'): void
    {
        ProjectMember::firstOrCreate(
            ['project_id' => $project->id, 'user_id' => $user->id],
            ['role' => $role],
        );
    }
}
