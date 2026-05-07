<?php

use App\Models\Company;
use App\Models\Hr\Department;
use App\Models\Tenant;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->companyA = makeCompany(['slug' => 'company-a-' . \Illuminate\Support\Str::random(4)]);
    $this->companyB = makeCompany(['slug' => 'company-b-' . \Illuminate\Support\Str::random(4)]);
    $this->tenantA  = makeTenant($this->companyA);
    $this->tenantB  = makeTenant($this->companyB);
});

// ---------- company_id auto-set when tenant guard active ----------

it('auto-sets company_id from tenant guard when creating with active tenant auth', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $dept = Department::create(['name' => 'Auto-scoped Dept']);

    expect($dept->company_id)->toBe($this->companyA->id);
});

it('sets company_id to tenant B company when tenant B is authenticated', function () {
    $this->actingAs($this->tenantB, 'tenant');

    $dept = Department::create(['name' => 'Company B Dept']);

    expect($dept->company_id)->toBe($this->companyB->id);
});

// ---------- company_id NOT auto-set without auth ----------

it('does NOT auto-set company_id when no auth guard is active', function () {
    // No actingAs — must provide company_id explicitly
    $dept = Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyA->id,
        'name'       => 'Explicit Company Dept',
    ]);

    expect($dept->company_id)->toBe($this->companyA->id);
});

it('explicit company_id is preserved even when tenant is authenticated', function () {
    $this->actingAs($this->tenantA, 'tenant');

    // Provide explicit company_id — it should be used instead of overriding from guard
    // BelongsToCompany only sets company_id if it's empty
    $dept = Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyB->id,
        'name'       => 'Explicit Override Dept',
    ]);

    expect($dept->company_id)->toBe($this->companyB->id);
});

// ---------- forCompany() bypasses global scope ----------

it('forCompany() returns records for the specified company without tenant guard', function () {
    Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyA->id,
        'name'       => 'CompanyA Only',
    ]);

    Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyB->id,
        'name'       => 'CompanyB Only',
    ]);

    $results = Department::forCompany($this->companyA->id)->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->name)->toBe('CompanyA Only');
});

it('forCompany() works independently of any active auth guard', function () {
    $this->actingAs($this->tenantA, 'tenant');

    Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyB->id,
        'name'       => 'B Dept Via ForCompany',
    ]);

    // Even though tenantA is authenticated (which would scope to companyA),
    // forCompany(companyB) explicitly bypasses and queries companyB
    $results = Department::forCompany($this->companyB->id)->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->name)->toBe('B Dept Via ForCompany');
});

// ---------- company() relation ----------

it('company() relationship returns the correct Company model', function () {
    Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyA->id,
        'name'       => 'Relation Test Dept',
    ]);

    $dept = Department::forCompany($this->companyA->id)->first();
    $dept->load('company');

    expect($dept->company->id)->toBe($this->companyA->id);
    expect($dept->company)->toBeInstanceOf(Company::class);
});

// ---------- creating hook uses API context ----------

it('creating hook falls back to api_company when no tenant guard is active', function () {
    // Simulate what AuthenticateApiKey does: set api_company on request attributes
    request()->attributes->set('api_company', $this->companyB);

    $dept = Department::withoutGlobalScopes()->create([
        'name' => 'API Created Dept',
    ]);

    expect($dept->company_id)->toBe($this->companyB->id);

    // Clean up attribute
    request()->attributes->remove('api_company');
});
