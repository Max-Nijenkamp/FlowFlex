<?php

use App\Models\Company;
use App\Models\File;
use App\Models\Tenant;
use App\Services\FileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');

    $this->company = makeCompany(['slug' => 'test-company']);
    $this->tenant  = makeTenant($this->company);
    $this->service = app(FileStorageService::class);
});

it('stores a file and returns a File model', function () {
    $this->actingAs($this->tenant, 'tenant');

    $upload = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $file = $this->service->store($upload, 'documents');

    expect($file)->toBeInstanceOf(File::class);
    expect($file->exists)->toBeTrue();
    expect($file->company_id)->toBe($this->company->id);
    expect($file->collection)->toBe('documents');
    expect($file->original_name)->toBe('document.pdf');
    expect($file->mime_type)->toBe('application/pdf');

    Storage::disk('local')->assertExists($file->path);
});

it('sets company_id from tenant guard on store', function () {
    $this->actingAs($this->tenant, 'tenant');

    $upload = UploadedFile::fake()->image('photo.jpg');
    $file   = $this->service->store($upload, 'photos');

    expect($file->company_id)->toBe($this->company->id);
});

it('sets uploaded_by_tenant_id from authenticated tenant', function () {
    $this->actingAs($this->tenant, 'tenant');

    $upload = UploadedFile::fake()->create('file.txt', 10, 'text/plain');
    $file   = $this->service->store($upload);

    expect($file->uploaded_by_tenant_id)->toBe($this->tenant->id);
});

it('uses custom disk when specified', function () {
    Storage::fake('s3');

    $this->actingAs($this->tenant, 'tenant');

    $upload = UploadedFile::fake()->create('doc.docx', 50, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    $file   = $this->service->store($upload, 'documents', 's3');

    expect($file->disk)->toBe('s3');
    Storage::disk('s3')->assertExists($file->path);
});

it('deletes a file from disk and soft-deletes the model', function () {
    $this->actingAs($this->tenant, 'tenant');

    $upload = UploadedFile::fake()->create('to-delete.pdf', 10, 'application/pdf');
    $file   = $this->service->store($upload);
    $path   = $file->path;

    $result = $this->service->delete($file);

    expect($result)->toBeTrue();
    Storage::disk('local')->assertMissing($path);
    expect(File::withTrashed()->find($file->id)?->deleted_at)->not->toBeNull();
});

it('temporaryUrl returns a URL string for a stored file', function () {
    $this->actingAs($this->tenant, 'tenant');

    $upload = UploadedFile::fake()->create('report.pdf', 20, 'application/pdf');
    $file   = $this->service->store($upload);

    $url = $this->service->temporaryUrl($file, 30);

    expect($url)->toBeString()->not->toBeEmpty();
});

it('stores files under company slug path', function () {
    $this->actingAs($this->tenant, 'tenant');

    $upload = UploadedFile::fake()->create('contract.pdf', 15, 'application/pdf');
    $file   = $this->service->store($upload, 'contracts');

    expect($file->path)->toContain('test-company');
    expect($file->path)->toContain('contracts');
});

it('File model url() method returns a non-empty string', function () {
    $this->actingAs($this->tenant, 'tenant');

    $upload = UploadedFile::fake()->create('test.pdf', 10, 'application/pdf');
    $file   = $this->service->store($upload);

    expect($file->url())->toBeString()->not->toBeEmpty();
});

it('raw File model without going through service has expected attributes', function () {
    $file = File::withoutGlobalScopes()->create([
        'company_id'    => $this->company->id,
        'disk'          => 'local',
        'path'          => 'companies/test/file.txt',
        'original_name' => 'file.txt',
        'mime_type'     => 'text/plain',
        'size'          => 100,
        'collection'    => 'default',
    ]);

    expect($file->humanSize())->toContain('B');
    expect($file->isImage())->toBeFalse();
});
