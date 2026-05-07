<?php

use App\Enums\Projects\TaskPriority;
use App\Enums\Projects\TaskStatus;
use App\Events\Projects\TaskAssigned;
use App\Listeners\Projects\NotifyAssigneeTaskAssigned;
use App\Models\Company;
use App\Models\Projects\Task;
use App\Models\Tenant;
use App\Notifications\Projects\TaskAssignedNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company  = makeCompany();
    $this->tenant   = makeTenant($this->company);
    $this->assignee = makeTenant($this->company, [
        'email' => 'assignee@test.com',
    ]);

    $this->task = Task::withoutGlobalScopes()->create([
        'company_id'          => $this->company->id,
        'title'               => 'Implement new feature',
        'status'              => TaskStatus::Todo->value,
        'priority'            => TaskPriority::High->value,
        'assignee_tenant_id'  => $this->assignee->id,
    ]);
});

// ============================================================
// TaskAssigned → NotifyAssigneeTaskAssigned
// ============================================================

it('TaskAssigned event is dispatched with correct task and assignee', function () {
    Event::fake();

    TaskAssigned::dispatch($this->task, $this->assignee);

    Event::assertDispatched(TaskAssigned::class, function ($event) {
        return $event->task->id === $this->task->id
            && $event->assignee->id === $this->assignee->id;
    });
});

it('EventServiceProvider maps TaskAssigned to NotifyAssigneeTaskAssigned', function () {
    Event::fake();

    event(new TaskAssigned($this->task, $this->assignee));

    Event::assertListening(TaskAssigned::class, NotifyAssigneeTaskAssigned::class);
});

it('NotifyAssigneeTaskAssigned sends notification to assignee', function () {
    Notification::fake();

    $listener = new NotifyAssigneeTaskAssigned();
    $listener->handle(new TaskAssigned($this->task, $this->assignee));

    Notification::assertSentTo($this->assignee, TaskAssignedNotification::class);
});

it('NotifyAssigneeTaskAssigned does nothing when task has no assignee', function () {
    Notification::fake();

    // Task with no assignee_tenant_id set — listener checks task->assignee first
    $unassignedTask = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Unassigned',
        'status'     => TaskStatus::Todo->value,
        'priority'   => TaskPriority::Medium->value,
    ]);

    $listener = new NotifyAssigneeTaskAssigned();
    $listener->handle(new TaskAssigned($unassignedTask, null));

    Notification::assertNothingSent();
});

it('TaskAssigned event holds correct task reference', function () {
    $event = new TaskAssigned($this->task, $this->assignee);

    expect($event->task->id)->toBe($this->task->id);
    expect($event->assignee->id)->toBe($this->assignee->id);
});

it('TaskAssigned can be dispatched when task has no assignee', function () {
    Event::fake();

    $task = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Unassigned Task',
        'status'     => TaskStatus::Backlog->value,
        'priority'   => TaskPriority::Low->value,
    ]);

    TaskAssigned::dispatch($task, null);

    Event::assertDispatched(TaskAssigned::class, function ($event) use ($task) {
        return $event->task->id === $task->id && $event->assignee === null;
    });
});

it('TaskAssignedNotification can be instantiated with task', function () {
    $notification = new TaskAssignedNotification($this->task);

    expect($notification)->toBeInstanceOf(TaskAssignedNotification::class);
});
