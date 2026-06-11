<?php

declare(strict_types=1);

use App\Actions\StartImportAction;
use App\Data\CreateImportData;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\DataImport;
use App\Models\User;
use App\Support\Import\ImporterInterface;
use App\Support\Import\ImporterRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

/** Importer test double writing into an in-memory collection. */
class FakeRecordImporter implements ImporterInterface
{
    /** @var list<array<string, mixed>> */
    public static array $imported = [];

    public function fields(): array
    {
        return ['name' => 'Name', 'email' => 'Email'];
    }

    public function requiredFields(): array
    {
        return ['name'];
    }

    public function importRow(array $row): void
    {
        if (blank($row['name'])) {
            throw new InvalidArgumentException('Name is required.');
        }

        self::$imported[] = $row;
    }
}

beforeEach(function () {
    Storage::fake();
    FakeRecordImporter::$imported = [];

    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');

    // Register a fake importer under an active module key.
    app(ImporterRegistry::class)->register('core.settings', FakeRecordImporter::class);
    CompanyModuleSubscription::factory()
        ->forCompany($this->company)->module('core.settings')->create();
});

function csvImport(string $content): DataImport
{
    Storage::put('companies/test/import.csv', $content);

    return StartImportAction::run(new CreateImportData(
        target: 'core.settings',
        stored_path: 'companies/test/import.csv',
        filename: 'import.csv',
        column_map: ['Full Name' => 'name', 'E-mail' => 'email'],
    ));
}

it('imports mapped rows and counts successes', function () {
    $import = csvImport("Full Name,E-mail\nMax,m@example.com\nEva,e@example.com");

    $fresh = $import->fresh();
    expect((string) $fresh->status)->toBe('complete')
        ->and($fresh->total_rows)->toBe(2)
        ->and($fresh->success_rows)->toBe(2)
        ->and(FakeRecordImporter::$imported)->toHaveCount(2)
        ->and(FakeRecordImporter::$imported[0]['name'])->toBe('Max');
});

it('records row failures in the error report and continues', function () {
    $import = csvImport("Full Name,E-mail\nMax,m@example.com\n,missing@example.com");

    $fresh = $import->fresh();
    expect($fresh->success_rows)->toBe(1)
        ->and($fresh->error_rows)->toBe(1)
        ->and((string) $fresh->status)->toBe('complete')
        ->and($fresh->error_report_path)->not->toBeNull();

    expect(Storage::get($fresh->error_report_path))->toContain('Name is required.');
});

it('rejects targets whose module is not active', function () {
    app(ImporterRegistry::class)->register('hr.profiles', FakeRecordImporter::class);

    StartImportAction::run(new CreateImportData(
        target: 'hr.profiles',
        stored_path: 'x.csv',
        filename: 'x.csv',
        column_map: ['A' => 'name'],
    ));
})->throws(ValidationException::class);

it('rejects unmapped required columns', function () {
    StartImportAction::run(new CreateImportData(
        target: 'core.settings',
        stored_path: 'x.csv',
        filename: 'x.csv',
        column_map: ['E-mail' => 'email'], // name missing
    ));
})->throws(ValidationException::class);
