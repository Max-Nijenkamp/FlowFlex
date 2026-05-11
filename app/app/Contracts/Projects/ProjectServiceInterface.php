<?php

declare(strict_types=1);

namespace App\Contracts\Projects;

use App\Data\Projects\CreateProjectData;
use App\Models\Projects\Project;
use App\Models\User;

interface ProjectServiceInterface
{
    public function create(CreateProjectData $data): Project;

    public function update(Project $project, array $data): Project;

    public function archive(Project $project): Project;

    public function addMember(Project $project, User $user, string $role = 'member'): void;
}
