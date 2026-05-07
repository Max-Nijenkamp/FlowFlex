<?php

use App\Models\Projects\Task;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);
});

it('can create a parent task', function () {
    $parent = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Parent Task',
        'status'     => 'todo',
        'priority'   => 'p3_medium',
    ]);

    expect($parent)->toBeInstanceOf(Task::class);
    expect($parent->title)->toBe('Parent Task');
});

it('can create a child task with parent_id', function () {
    $parent = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Parent Task',
        'status'     => 'todo',
        'priority'   => 'p3_medium',
    ]);

    $child = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'parent_id'  => $parent->id,
        'title'      => 'Child Task',
        'status'     => 'todo',
        'priority'   => 'p4_low',
    ]);

    expect($child->parent_id)->toBe($parent->id);
});

it('child task parent() relationship resolves to parent task', function () {
    $parent = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Parent Task',
        'status'     => 'todo',
        'priority'   => 'p3_medium',
    ]);

    $child = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'parent_id'  => $parent->id,
        'title'      => 'Child Task',
        'status'     => 'todo',
        'priority'   => 'p4_low',
    ]);

    expect($child->parent)->toBeInstanceOf(Task::class);
    expect($child->parent->id)->toBe($parent->id);
});

it('parent task children() relationship returns child tasks', function () {
    $parent = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Parent Task',
        'status'     => 'todo',
        'priority'   => 'p3_medium',
    ]);

    Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'parent_id'  => $parent->id,
        'title'      => 'Child Task 1',
        'status'     => 'todo',
        'priority'   => 'p4_low',
    ]);

    Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'parent_id'  => $parent->id,
        'title'      => 'Child Task 2',
        'status'     => 'todo',
        'priority'   => 'p4_low',
    ]);

    expect($parent->children()->count())->toBe(2);
});

it('parent_id is nullable and defaults to null', function () {
    $task = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Standalone Task',
        'status'     => 'todo',
        'priority'   => 'p3_medium',
    ]);

    expect($task->parent_id)->toBeNull();
});

it('task is scoped to company', function () {
    $parent = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Scoped Parent',
        'status'     => 'todo',
        'priority'   => 'p3_medium',
    ]);

    $child = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'parent_id'  => $parent->id,
        'title'      => 'Scoped Child',
        'status'     => 'todo',
        'priority'   => 'p4_low',
    ]);

    $this->actingAs($this->tenant, 'tenant');

    expect(Task::find($child->id))->not->toBeNull();
    expect(Task::find($child->id)->company_id)->toBe($this->company->id);
});
