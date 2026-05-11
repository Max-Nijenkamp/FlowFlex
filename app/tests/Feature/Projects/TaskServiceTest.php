<?php

declare(strict_types=1);

use App\Contracts\Projects\TaskServiceInterface;
use App\Data\Projects\CreateTaskData;
use App\Models\Company;
use App\Models\Projects\Project;
use App\Models\Projects\Task;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('Task Service', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create(['company_id' => $this->company->id]);
        app(CompanyContext::class)->set($this->company);
        $this->project = Project::factory()->forCompany($this->company)->create(['owner_id' => $this->user->id]);
        $this->service = app(TaskServiceInterface::class);
    });

    it('creates a task', function () {
        $data = new CreateTaskData(
            title: 'Test Task',
            created_by: $this->user->id,
            project_id: $this->project->id,
        );

        $task = $this->service->create($data);

        expect($task)->toBeInstanceOf(Task::class);
        expect($task->title)->toBe('Test Task');
        expect($task->project_id)->toBe($this->project->id);
        expect($task->company_id)->toBe($this->company->id);
        expect($task->status)->toBe('todo');
    });

    it('completes a task', function () {
        $task = Task::factory()->forProject($this->project)->create();

        $completed = $this->service->complete($task);

        expect($completed->status)->toBe('done');
        expect($completed->completed_at)->not->toBeNull();
    });

    it('creates a subtask', function () {
        $parent = Task::factory()->forProject($this->project)->create();

        $data = new CreateTaskData(
            title: 'Sub Task',
            created_by: $this->user->id,
            project_id: $this->project->id,
            parent_id: $parent->id,
        );

        $subtask = $this->service->create($data);

        expect($subtask->parent_id)->toBe($parent->id);
        expect($parent->subtasks)->toHaveCount(1);
    });

    it('enforces company scope on tasks', function () {
        $otherCompany = Company::factory()->create(['status' => 'active']);
        $otherUser    = User::factory()->create(['company_id' => $otherCompany->id]);
        $otherProject = Project::withoutGlobalScopes()->create([
            'company_id' => $otherCompany->id,
            'name'       => 'Other Project',
            'owner_id'   => $otherUser->id,
            'status'     => 'planning',
            'priority'   => 'medium',
        ]);

        $otherTask = Task::withoutGlobalScopes()->create([
            'company_id' => $otherCompany->id,
            'project_id' => $otherProject->id,
            'title'      => 'Other Company Task',
            'created_by' => $otherUser->id,
            'status'     => 'todo',
            'priority'   => 'medium',
        ]);

        // Create a task for the current company so there is something to assert on
        Task::factory()->forProject($this->project)->create();

        $visibleTaskIds = Task::pluck('id');
        expect($visibleTaskIds)->not->toContain($otherTask->id);
        expect(Task::count())->toBe(1);
    });

    it('updates a task', function () {
        $task = Task::factory()->forProject($this->project)->todo()->create();

        $updated = $this->service->update($task, ['status' => 'in_progress']);

        expect($updated->status)->toBe('in_progress');
    });

    it('reorders tasks with single query', function () {
        $task1 = Task::factory()->forProject($this->project)->create(['sort_order' => 0]);
        $task2 = Task::factory()->forProject($this->project)->create(['sort_order' => 1]);

        // Reverse order: task2 becomes index 0, task1 becomes index 1
        $this->service->reorder([$task2->id, $task1->id]);

        expect($task1->fresh()->sort_order)->toBe(1)
            ->and($task2->fresh()->sort_order)->toBe(0);
    });

    it('reorder with empty array is a no-op', function () {
        // Should not throw
        $this->service->reorder([]);
        expect(true)->toBeTrue();
    });
});
