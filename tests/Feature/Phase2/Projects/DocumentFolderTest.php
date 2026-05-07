<?php

use App\Models\Company;
use App\Models\Projects\DocumentFolder;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'projects', 'projects');
    givePermissions($this->tenant, [
        'projects.document-folders.view',
        'projects.document-folders.create',
        'projects.document-folders.edit',
        'projects.document-folders.delete',
    ]);

    $this->folder = DocumentFolder::withoutGlobalScopes()->create([
        'company_id'           => $this->company->id,
        'name'                 => 'Root Folder',
        'created_by_tenant_id' => $this->tenant->id,
        'is_system'            => false,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list document folders', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/projects/document-folders')
        ->assertOk();
});

it('unauthenticated request redirects from document folders list', function () {
    $this->get('/projects/document-folders')->assertRedirect();
});

it('tenant without permission gets 403 on document folders list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/projects/document-folders')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a document folder', function () {
    $folder = DocumentFolder::withoutGlobalScopes()->create([
        'company_id'           => $this->company->id,
        'name'                 => 'Marketing',
        'created_by_tenant_id' => $this->tenant->id,
        'is_system'            => false,
    ]);

    expect($folder->exists)->toBeTrue();
    expect($folder->name)->toBe('Marketing');
});

// ---------- Edit ----------

it('can update a document folder', function () {
    $this->folder->update(['name' => 'Archived Folder']);

    expect($this->folder->fresh()->name)->toBe('Archived Folder');
});

// ---------- Nested folders ----------

it('can create a nested folder structure', function () {
    $child = DocumentFolder::withoutGlobalScopes()->create([
        'company_id'           => $this->company->id,
        'name'                 => 'Sub Folder',
        'parent_folder_id'     => $this->folder->id,
        'created_by_tenant_id' => $this->tenant->id,
        'is_system'            => false,
    ]);

    expect($child->parent_folder_id)->toBe($this->folder->id);
    expect($this->folder->children()->count())->toBe(1);
});

// ---------- Delete ----------

it('can soft-delete a document folder', function () {
    $this->folder->delete();

    expect($this->folder->trashed())->toBeTrue();
    expect(DocumentFolder::withTrashed()->withoutGlobalScopes()->find($this->folder->id))->not->toBeNull();
});

it('soft-deleted document folders do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->folder->delete();

    expect(DocumentFolder::all()->pluck('id'))->not->toContain($this->folder->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see document folders from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'projects.document-folders.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(DocumentFolder::all()->pluck('id'))->not->toContain($this->folder->id);
});
