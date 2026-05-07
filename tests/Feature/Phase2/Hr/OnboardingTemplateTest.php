<?php

use App\Models\Company;
use App\Models\Hr\OnboardingTemplate;
use App\Models\Hr\OnboardingTemplateTask;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.onboarding-templates.view',
        'hr.onboarding-templates.create',
        'hr.onboarding-templates.edit',
        'hr.onboarding-templates.delete',
    ]);

    $this->template = OnboardingTemplate::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'name'        => 'Standard Onboarding',
        'description' => 'Default template',
        'is_active'   => true,
    ]);

    $this->templateTask = OnboardingTemplateTask::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'template_id' => $this->template->id,
        'title'       => 'Sign Contract',
        'task_type'   => 'document_upload',
        'sort_order'  => 1,
        'is_required' => true,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list onboarding templates', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/onboarding-templates')
        ->assertOk();
});

it('unauthenticated request redirects from onboarding templates list', function () {
    $this->get('/hr/onboarding-templates')->assertRedirect();
});

it('tenant without permission gets 403 on onboarding templates list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/onboarding-templates')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create an onboarding template record', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Hr\Resources\OnboardingTemplateResource\Pages\CreateOnboardingTemplate::class)
        ->fillForm([
            'name'      => 'Engineer Onboarding',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(OnboardingTemplate::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Engineer Onboarding')
        ->exists()
    )->toBeTrue();
});

// ---------- Edit ----------

it('can update an onboarding template', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Hr\Resources\OnboardingTemplateResource\Pages\EditOnboardingTemplate::class,
            ['record' => $this->template->getRouteKey()]
        )
        ->fillForm(['name' => 'Enhanced Onboarding'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->template->fresh()->name)->toBe('Enhanced Onboarding');
});

// ---------- TemplateTasksRelationManager ----------

it('template has tasks relationship loaded correctly', function () {
    $this->template->load('tasks');

    expect($this->template->tasks)->toHaveCount(1);
    expect($this->template->tasks->first()->title)->toBe('Sign Contract');
});

it('can create a template task via model', function () {
    OnboardingTemplateTask::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'template_id' => $this->template->id,
        'title'       => 'Watch Safety Video',
        'task_type'   => 'training_course',
        'sort_order'  => 2,
        'is_required' => false,
    ]);

    expect($this->template->tasks()->count())->toBe(2);
});

it('template tasks are ordered by sort_order', function () {
    OnboardingTemplateTask::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'template_id' => $this->template->id,
        'title'       => 'First Task',
        'task_type'   => 'form_fill',
        'sort_order'  => 0,
        'is_required' => true,
    ]);

    $tasks = $this->template->tasks()->get();
    expect($tasks->first()->sort_order)->toBe(0);
    expect($tasks->last()->sort_order)->toBe(1);
});

// ---------- Delete ----------

it('can soft-delete an onboarding template', function () {
    $this->template->delete();

    expect($this->template->trashed())->toBeTrue();
    expect(OnboardingTemplate::withTrashed()->withoutGlobalScopes()->find($this->template->id))->not->toBeNull();
});

it('soft-deleted templates do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->template->delete();

    expect(OnboardingTemplate::all()->pluck('id'))->not->toContain($this->template->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see templates from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.onboarding-templates.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(OnboardingTemplate::all()->pluck('id'))->not->toContain($this->template->id);
});
