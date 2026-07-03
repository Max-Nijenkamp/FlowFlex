<?php

declare(strict_types=1);

use App\Models\Company;

test('a soft-deleted model is excluded from default queries and is restorable', function () {
    $company = Company::factory()->create();

    $company->delete();

    expect(Company::find($company->id))->toBeNull()
        ->and(Company::withTrashed()->find($company->id))->not->toBeNull()
        ->and($company->fresh()->deleted_at)->not->toBeNull();

    $company->restore();

    expect(Company::find($company->id))->not->toBeNull();
});

test('ordinary delete sets deleted_at; forceDelete actually removes the row', function () {
    $company = Company::factory()->create();

    $company->delete();
    expect(Company::withTrashed()->whereKey($company->id)->exists())->toBeTrue();

    $company->forceDelete();
    expect(Company::withTrashed()->whereKey($company->id)->exists())->toBeFalse();
});
