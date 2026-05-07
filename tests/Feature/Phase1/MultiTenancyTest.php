<?php

use App\Models\Company;
use App\Models\Hr\Department;
use App\Models\Tenant;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->companyA = makeCompany(['name' => 'Company A', 'slug' => 'company-a']);
    $this->companyB = makeCompany(['name' => 'Company B', 'slug' => 'company-b']);

    $this->tenantA = makeTenant($this->companyA);
    $this->tenantB = makeTenant($this->companyB);

    // Departments created without an active tenant guard — must set company_id explicitly
    $this->deptA = Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyA->id,
        'name'       => 'Engineering A',
    ]);

    $this->deptB = Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyB->id,
        'name'       => 'Engineering B',
    ]);
});

it('tenant A can see own company departments via global scope', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $departments = Department::all();

    expect($departments)->toHaveCount(1);
    expect($departments->first()->id)->toBe($this->deptA->id);
});

it('tenant B can see own company departments via global scope', function () {
    $this->actingAs($this->tenantB, 'tenant');

    $departments = Department::all();

    expect($departments)->toHaveCount(1);
    expect($departments->first()->id)->toBe($this->deptB->id);
});

it('global scope prevents cross-company data leaks', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $ids = Department::pluck('id')->all();

    expect($ids)->not->toContain($this->deptB->id);
});

it('withoutGlobalScopes returns all companies data', function () {
    $all = Department::withoutGlobalScopes()->get();

    expect($all)->toHaveCount(2);
});

it('forCompany() scopes to the correct company without active tenant guard', function () {
    $results = Department::forCompany($this->companyA->id)->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($this->deptA->id);
});

it('creating a record auto-sets company_id from tenant guard', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $dept = Department::create(['name' => 'Auto Company']);

    expect($dept->company_id)->toBe($this->companyA->id);
});

it('creating without tenant guard requires explicit company_id', function () {
    $dept = Department::withoutGlobalScopes()->create([
        'company_id' => $this->companyB->id,
        'name'       => 'Explicit Company',
    ]);

    expect($dept->company_id)->toBe($this->companyB->id);
});

it('soft-deleted records do not appear in tenant-scoped queries', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $this->deptA->delete();

    expect(Department::all())->toHaveCount(0);
    expect(Department::withTrashed()->count())->toBe(1);
});

it('tenants cannot find records from other companies by id', function () {
    $this->actingAs($this->tenantA, 'tenant');

    $found = Department::find($this->deptB->id);

    expect($found)->toBeNull();
});
