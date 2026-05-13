<?php

use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;

it('prevents company A from seeing company B data', function () {
    $companyA = createCompany();
    $companyB = createCompany();

    // Create users for both companies (without CompanyContext set — factories handle company_id directly)
    app(CompanyContext::class)->clear();

    $userA = User::factory()->create(['company_id' => $companyA->id, 'status' => 'active']);
    $userB = User::factory()->create(['company_id' => $companyB->id, 'status' => 'active']);

    // Set context to Company A
    app(CompanyContext::class)->set($companyA);

    // Scoped query should only return Company A users
    $visibleUsers = User::all();
    expect($visibleUsers->pluck('id')->contains($userA->id))->toBeTrue();
    expect($visibleUsers->pluck('id')->contains($userB->id))->toBeFalse();
});

it('company B record exists without scope but is hidden with scope', function () {
    $companyA = createCompany();
    $companyB = createCompany();

    app(CompanyContext::class)->clear();
    $userB = User::factory()->create(['company_id' => $companyB->id]);

    // Without scope — record exists
    $existsWithoutScope = User::withoutGlobalScopes()->where('id', $userB->id)->exists();
    expect($existsWithoutScope)->toBeTrue();

    // With Company A scope — record is not visible
    app(CompanyContext::class)->set($companyA);
    $existsWithScope = User::where('id', $userB->id)->exists();
    expect($existsWithScope)->toBeFalse();
});

it('company context is clear between tests', function () {
    $context = app(CompanyContext::class);
    expect($context->hasCompany())->toBeFalse();
});

it('companies table has both records without scope', function () {
    $companyA = createCompany();
    $companyB = createCompany();

    // Companies model doesn't use BelongsToCompany, so both are always visible
    expect(Company::where('id', $companyA->id)->exists())->toBeTrue();
    expect(Company::where('id', $companyB->id)->exists())->toBeTrue();
});

it('company_id is automatically set from context when creating a user', function () {
    $company = createCompany();
    app(CompanyContext::class)->set($company);

    // Create without explicit company_id — should auto-set from context
    $user = User::factory()->make();
    $user->company_id = null; // Force it to be null to test auto-set
    $user->save();

    expect($user->company_id)->toBe($company->id);
});
