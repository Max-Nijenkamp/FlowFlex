<?php

use App\Models\Company;
use App\Models\Projects\TaskLabel;
use App\Models\Tenant;
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

    $this->label = TaskLabel::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Bug',
        'color'      => '#ff0000',
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list task labels', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/projects/task-labels')
        ->assertOk();
});

it('unauthenticated request redirects from task labels list', function () {
    $this->get('/projects/task-labels')->assertRedirect();
});

it('tenant without permission gets 403 on task labels list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/projects/task-labels')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a task label record', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('projects');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Projects\Resources\TaskLabelResource\Pages\CreateTaskLabel::class)
        ->fillForm([
            'name'  => 'Feature',
            'color' => '#00ff00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(TaskLabel::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Feature')
        ->exists()
    )->toBeTrue();
});

// ---------- Edit ----------

it('can update a task label', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('projects');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Projects\Resources\TaskLabelResource\Pages\EditTaskLabel::class,
            ['record' => $this->label->getRouteKey()]
        )
        ->fillForm(['name' => 'Defect'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->label->fresh()->name)->toBe('Defect');
});

// ---------- Delete ----------

it('can soft-delete a task label', function () {
    $this->label->delete();

    expect($this->label->trashed())->toBeTrue();
    expect(TaskLabel::withTrashed()->withoutGlobalScopes()->find($this->label->id))->not->toBeNull();
});

it('soft-deleted task labels do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->label->delete();

    expect(TaskLabel::all()->pluck('id'))->not->toContain($this->label->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see task labels from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'projects.tasks.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(TaskLabel::all()->pluck('id'))->not->toContain($this->label->id);
});
