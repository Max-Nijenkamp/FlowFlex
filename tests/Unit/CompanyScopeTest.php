<?php

use App\Models\Company;
use App\Models\Hr\Department;
use App\Models\Tenant;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->companyA = makeCompany(['slug' => 'scope-a-' . \Illuminate\Support\Str::random(4)]);
    $this->companyB = makeCompany(['slug' => 'scope-b-' . \Illuminate\Support\Str::random(4)]);

    $this->tenantA = makeTenant($this->companyA);
    $this->tenantB = makeTenant($this->companyB);

    Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyA->id,
        'name'       => 'Dept Alpha',
    ]);

    Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyA->id,
        'name'       => 'Dept Beta',
    ]);

    Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyB->id,
        'name'       => 'Dept Gamma',
    ]);
});

// ---------- Scope applies when tenant auth is active ----------

it('CompanyScope filters to tenant authenticated company only', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $results = Department::all();

    expect($results)->toHaveCount(2);
    expect($results->pluck('name'))->toContain('Dept Alpha');
    expect($results->pluck('name'))->toContain('Dept Beta');
    expect($results->pluck('name'))->not->toContain('Dept Gamma');
});

it('CompanyScope filters to company B when tenant B is active', function () {
    $this->actingAs($this->tenantB, 'tenant');

    $results = Department::all();

    expect($results)->toHaveCount(1);
    expect($results->first()->name)->toBe('Dept Gamma');
});

// ---------- Scope is a no-op without tenant auth ----------

it('CompanyScope is no-op without active tenant guard', function () {
    // No actingAs — scope should not filter at all
    $results = Department::withoutGlobalScopes()->get();

    expect($results)->toHaveCount(3);
});

it('withoutGlobalScopes bypasses CompanyScope even with active tenant auth', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $results = Department::withoutGlobalScopes()->get();

    expect($results)->toHaveCount(3);
});

// ---------- Count verification ----------

it('count() respects CompanyScope', function () {
    $this->actingAs($this->tenantA, 'tenant');

    expect(Department::count())->toBe(2);
});

it('count() without global scopes returns all records', function () {
    expect(Department::withoutGlobalScopes()->count())->toBe(3);
});

// ---------- find() respects CompanyScope ----------

it('find() on a record from another company returns null when tenant A is active', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $deptGamma = Department::withoutGlobalScopes()->where('name', 'Dept Gamma')->first();

    expect(Department::find($deptGamma->id))->toBeNull();
});

it('find() on own company record succeeds', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $deptAlpha = Department::withoutGlobalScopes()->where('name', 'Dept Alpha')->first();

    expect(Department::find($deptAlpha->id))->not->toBeNull();
});

// ---------- pluck respects scope ----------

it('pluck() respects company scope', function () {
    $this->actingAs($this->tenantB, 'tenant');

    $names = Department::pluck('name')->all();

    expect($names)->toContain('Dept Gamma');
    expect($names)->not->toContain('Dept Alpha');
});
