<?php

declare(strict_types=1);

namespace App\Providers\Projects;

use App\Contracts\Projects\ProjectServiceInterface;
use App\Contracts\Projects\SprintServiceInterface;
use App\Contracts\Projects\TaskServiceInterface;
use App\Contracts\Projects\TimeEntryServiceInterface;
use App\Services\Projects\ProjectService;
use App\Services\Projects\SprintService;
use App\Services\Projects\TaskService;
use App\Services\Projects\TimeEntryService;
use Illuminate\Support\ServiceProvider;

class ProjectsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProjectServiceInterface::class, ProjectService::class);
        $this->app->bind(TaskServiceInterface::class, TaskService::class);
        $this->app->bind(SprintServiceInterface::class, SprintService::class);
        $this->app->bind(TimeEntryServiceInterface::class, TimeEntryService::class);
    }

    public function boot(): void
    {
        //
    }
}
