<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

it('creates a company with a ULID primary key', function () {
    $company = Company::factory()->create();

    expect($company->id)->toBeString()
        ->and(Str::isUlid($company->id))->toBeTrue();
});

it('creates a user scoped to a company', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    expect(Str::isUlid($user->id))->toBeTrue()
        ->and($user->company_id)->toBe($company->id)
        ->and($user->company->is($company))->toBeTrue();
});

it('exposes the user full name accessor', function () {
    $user = User::factory()->create(['first_name' => 'Max', 'last_name' => 'Nijenkamp']);

    expect($user->full_name)->toBe('Max Nijenkamp');
});

it('creates an admin that is not company-scoped', function () {
    $admin = Admin::factory()->create();

    expect(Str::isUlid($admin->id))->toBeTrue()
        ->and($admin->getAttributes())->not->toHaveKey('company_id');
});

it('enforces unique email per company', function () {
    $company = Company::factory()->create();
    User::factory()->forCompany($company)->create(['email' => 'dupe@example.com']);

    User::factory()->forCompany($company)->create(['email' => 'dupe@example.com']);
})->throws(QueryException::class);
