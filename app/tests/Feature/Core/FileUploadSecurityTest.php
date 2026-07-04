<?php

declare(strict_types=1);

use App\Actions\TemporaryUrlAction;
use App\Models\Company;
use App\Models\User;
use App\Settings\CompanyBusinessSettings;
use App\Support\Services\FileStorageService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\Support\MediaProbeModel;

beforeEach(function (): void {
    MediaProbeModel::migrate();
    Storage::fake('public');
});

test('a forbidden extension is rejected', function () {
    setCompany(Company::factory()->create());

    $file = UploadedFile::fake()->create('shell.php', 10, 'application/x-php');

    expect(fn () => app(FileStorageService::class)->validateUpload($file))
        ->toThrow(ValidationException::class);
});

test('a MIME and extension mismatch is rejected', function () {
    setCompany(Company::factory()->create());

    // PNG bytes wearing a .pdf name
    $file = UploadedFile::fake()->image('report.png');
    $renamed = new UploadedFile($file->getPathname(), 'report.pdf', 'image/png', null, true);

    expect(fn () => app(FileStorageService::class)->validateUpload($renamed))
        ->toThrow(ValidationException::class);
});

test('an oversize file is rejected per the company setting', function () {
    setCompany(Company::factory()->create());

    $settings = app(CompanyBusinessSettings::class);
    $settings->max_upload_mb = 1;
    $settings->save();

    $tooBig = UploadedFile::fake()->create('big.pdf', 2048, 'application/pdf'); // 2 MB

    expect(fn () => app(FileStorageService::class)->validateUpload($tooBig))
        ->toThrow(ValidationException::class);

    $fine = UploadedFile::fake()->create('ok.pdf', 512, 'application/pdf');
    app(FileStorageService::class)->validateUpload($fine);
    expect(true)->toBeTrue();
});

test('a temporary url downloads within the hour and expires after it', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();
    $probe = MediaProbeModel::query()->create(['name' => 'probe']);
    $media = $probe->addMedia(UploadedFile::fake()->image('doc.jpg'))->toMediaCollection();

    $url = TemporaryUrlAction::run($media);

    $this->actingAs($user)->get($url)->assertOk();

    $this->travel(61)->minutes();
    $this->actingAs($user)->get($url)->assertForbidden();
});

test('company A cannot mint or use a temporary url for company B media', function () {
    $companyB = setCompany(Company::factory()->create());
    $probe = MediaProbeModel::query()->create(['name' => 'b-file']);
    $media = $probe->addMedia(UploadedFile::fake()->image('secret.jpg'))->toMediaCollection();
    $url = TemporaryUrlAction::run($media);

    $companyA = setCompany(Company::factory()->create());
    $intruder = User::factory()->for($companyA)->create();

    expect(fn () => TemporaryUrlAction::run($media))->toThrow(AuthorizationException::class);

    // CompanyScope hides B's media from A's route binding entirely — 404,
    // which leaks even less than a 403 would.
    $this->actingAs($intruder)->get($url)->assertNotFound();
});
