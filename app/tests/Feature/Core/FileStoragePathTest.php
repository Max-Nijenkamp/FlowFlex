<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Media;
use App\Support\Media\CompanyPathGenerator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Support\MediaProbeModel;

beforeEach(function (): void {
    MediaProbeModel::migrate();
    Storage::fake('public');
});

test('every stored file lands under companies/{company_id}/', function () {
    $company = setCompany(Company::factory()->create());
    $probe = MediaProbeModel::query()->create(['name' => 'probe']);

    $media = $probe->addMedia(UploadedFile::fake()->image('avatar.jpg'))
        ->toMediaCollection();

    expect($media->getPathRelativeToRoot())
        ->toStartWith("companies/{$company->id}/media_probes/{$probe->id}/");

    Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
});

test('conversions and responsive images carry the tenant prefix too', function () {
    $company = setCompany(Company::factory()->create());
    $probe = MediaProbeModel::query()->create(['name' => 'probe']);

    $media = $probe->addMedia(UploadedFile::fake()->image('avatar.jpg'))->toMediaCollection();
    $generator = new CompanyPathGenerator;

    expect($generator->getPathForConversions($media))
        ->toStartWith("companies/{$company->id}/")
        ->toContain('/conversions/')
        ->and($generator->getPathForResponsiveImages($media))
        ->toStartWith("companies/{$company->id}/")
        ->toContain('/responsive-images/');
});

test('a media row without company_id fails closed', function () {
    $media = new Media;
    $media->model_type = MediaProbeModel::class;
    $media->model_id = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    expect(fn () => (new CompanyPathGenerator)->getPath($media))
        ->toThrow(RuntimeException::class);
});
