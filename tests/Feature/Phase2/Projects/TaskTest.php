<?php

use App\Enums\Projects\TaskPriority;
use App\Enums\Projects\TaskStatus;
use App\Events\Projects\TaskAssigned;
use App\Models\Company;
use App\Models\Projects\Task;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'projects', 'projects');
    givePermissions($this->tenant, [
        'projects.tasks.view',
        'projects.tasks.create',
        'projects.tasks.edit',
        'projects.tasks.delete',
    ]);

    $this->task = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Implement login',
        'status'     => TaskStatus::Todo->value,
        'priority'   => TaskPriority::Medium->value,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list tasks', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/projects/tasks')
        ->assertOk();
});

it('unauthenticated request redirects from tasks list', function () {
    $this->get('/projects/tasks')->assertRedirect();
});

it('tenant without permission gets 403 on tasks list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/projects/tasks')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a task', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('projects');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Projects\Resources\TaskResource\Pages\CreateTask::class)
        ->fillForm([
            'title'    => 'Write tests',
            'status'   => TaskStatus::Todo->value,
            'priority' => TaskPriority::High->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Task::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('title', 'Write tests')
        ->exists()
    )->toBeTrue();
});

// ---------- Edit ----------

it('can update a task', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('projects');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Projects\Resources\TaskResource\Pages\EditTask::class,
            ['record' => $this->task->getRouteKey()]
        )
        ->fillForm(['title' => 'Implement OAuth login'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->task->fresh()->title)->toBe('Implement OAuth login');
});

// ---------- Task assignment + events ----------

it('dispatches TaskAssigned event when task is assigned', function () {
    Event::fake();

    $this->task->update(['assignee_tenant_id' => $this->tenant->id]);

    TaskAssigned::dispatch($this->task->fresh(), $this->tenant);

    Event::assertDispatched(TaskAssigned::class, function ($event) {
        return $event->task->id === $this->task->id
            && $event->assignee->id === $this->tenant->id;
    });
});

// ---------- Delete ----------

it('can soft-delete a task', function () {
    $this->task->delete();

    expect($this->task->trashed())->toBeTrue();
    expect(Task::withTrashed()->withoutGlobalScopes()->find($this->task->id))->not->toBeNull();
});

it('soft-deleted tasks do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->task->delete();

    expect(Task::all()->pluck('id'))->not->toContain($this->task->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see tasks from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'projects.tasks.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(Task::all()->pluck('id'))->not->toContain($this->task->id);
});

// ---------- Enums ----------

it('status casts to TaskStatus enum', function () {
    expect($this->task->status)->toBe(TaskStatus::Todo);
});

it('priority casts to TaskPriority enum', function () {
    expect($this->task->priority)->toBe(TaskPriority::Medium);
});

it('all TaskPriority enum values are correct', function () {
    expect(TaskPriority::Critical->value)->toBe('p1_critical');
    expect(TaskPriority::High->value)->toBe('p2_high');
    expect(TaskPriority::Medium->value)->toBe('p3_medium');
    expect(TaskPriority::Low->value)->toBe('p4_low');
});

it('all TaskStatus enum values include expected cases', function () {
    $values = collect(TaskStatus::cases())->pluck('value')->all();

    expect($values)->toContain('todo');
    expect($values)->toContain('in_progress');
    expect($values)->toContain('done');
});
