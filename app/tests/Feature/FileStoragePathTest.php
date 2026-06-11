<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use App\Support\Media\CompanyPathGenerator;
use App\Support\Services\FileStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

uses(RefreshDatabase::class);

it('builds tenant-scoped paths for any model', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    $path = app(FileStorageService::class)->pathFor($user, 'avatar.png');

    expect($path)->toBe("companies/{$company->id}/users/{$user->id}/avatar.png")
        ->and($path)->toStartWith("companies/{$company->id}/");
});

it('the media path generator prefixes companies/{id} including conversions', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    $media = new Media;
    $media->model_type = $user->getMorphClass();
    $media->model_id = $user->id;
    $media->setRelation('model', $user);

    $generator = new CompanyPathGenerator;

    expect($generator->getPath($media))->toStartWith("companies/{$company->id}/users/{$user->id}")
        ->and($generator->getPathForConversions($media))->toContain('/conversions/')
        ->and($generator->getPathForConversions($media))->toStartWith("companies/{$company->id}/");
});

it('media without a company owner falls back to the platform prefix', function () {
    $media = new Media;
    $media->model_id = '01TEST';

    expect((new CompanyPathGenerator)->getPath($media))->toStartWith('companies/platform/');
});
