<?php

use App\Models\Company;
use App\Models\Projects\Document;
use App\Models\Projects\DocumentFolder;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'projects', 'projects');
    givePermissions($this->tenant, [
        'projects.documents.view',
        'projects.documents.create',
        'projects.documents.edit',
        'projects.documents.delete',
    ]);

    $this->folder = DocumentFolder::withoutGlobalScopes()->create([
        'company_id'           => $this->company->id,
        'name'                 => 'Contracts',
        'created_by_tenant_id' => $this->tenant->id,
        'is_system'            => false,
    ]);

    $this->document = Document::withoutGlobalScopes()->create([
        'company_id'             => $this->company->id,
        'folder_id'              => $this->folder->id,
        'title'                  => 'NDA Agreement',
        'original_filename'      => 'nda.pdf',
        'mime_type'              => 'application/pdf',
        'file_size_bytes'        => 204800,
        'version_number'         => 1,
        'uploaded_by_tenant_id'  => $this->tenant->id,
        'is_starred'             => false,
        'tags'                   => ['legal', 'contract'],
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list documents', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/projects/documents')
        ->assertOk();
});

it('unauthenticated request redirects from documents list', function () {
    $this->get('/projects/documents')->assertRedirect();
});

it('tenant without permission gets 403 on documents list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/projects/documents')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a document record', function () {
    $doc = Document::withoutGlobalScopes()->create([
        'company_id'            => $this->company->id,
        'folder_id'             => $this->folder->id,
        'title'                 => 'Employment Contract',
        'original_filename'     => null, // nullable as per migration 500000
        'mime_type'             => 'application/pdf',
        'file_size_bytes'       => 102400,
        'version_number'        => 1,
        'uploaded_by_tenant_id' => $this->tenant->id,
        'is_starred'            => false,
    ]);

    expect($doc->exists)->toBeTrue();
    expect($doc->title)->toBe('Employment Contract');
    expect($doc->original_filename)->toBeNull();
});

// ---------- Edit ----------

it('can update a document', function () {
    $this->document->update(['title' => 'Updated NDA']);

    expect($this->document->fresh()->title)->toBe('Updated NDA');
});

it('can star a document', function () {
    $this->document->update(['is_starred' => true]);

    expect($this->document->fresh()->is_starred)->toBeTrue();
});

// ---------- Tags ----------

it('tags are stored as array', function () {
    $this->document->refresh();

    expect($this->document->tags)->toBeArray();
    expect($this->document->tags)->toContain('legal');
});

// ---------- Delete ----------

it('can soft-delete a document', function () {
    $this->document->delete();

    expect($this->document->trashed())->toBeTrue();
    expect(Document::withTrashed()->withoutGlobalScopes()->find($this->document->id))->not->toBeNull();
});

it('soft-deleted documents do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->document->delete();

    expect(Document::all()->pluck('id'))->not->toContain($this->document->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see documents from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'projects.documents.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(Document::all()->pluck('id'))->not->toContain($this->document->id);
});

// ---------- Folder relationship ----------

it('document belongs to the correct folder', function () {
    $this->document->load('folder');

    expect($this->document->folder->id)->toBe($this->folder->id);
});
