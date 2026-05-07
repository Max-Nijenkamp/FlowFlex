<?php

use App\Models\ApiKey;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Module;
use App\Models\Projects\Task;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    ['key' => $this->plainKey, 'model' => $this->apiKey] = makeApiKey($this->company);
});

// ---------- Health endpoint (public) ----------

it('health endpoint is accessible without auth', function () {
    $this->getJson('/api/v1/health')
        ->assertOk()
        ->assertJson(['status' => 'ok']);
});

// ---------- /me endpoint ----------

it('GET /me returns 401 when no API key provided', function () {
    $this->getJson('/api/v1/me')
        ->assertUnauthorized();
});

it('GET /me returns 401 when API key is invalid', function () {
    $this->withHeaders(['X-API-Key' => 'ff_invalid_key'])
        ->getJson('/api/v1/me')
        ->assertUnauthorized();
});

it('GET /me returns company data with valid API key', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonPath('company.id', $this->company->id)
        ->assertJsonPath('company.name', $this->company->name);
});

it('GET /me data is scoped to the company of the API key', function () {
    $otherCompany = makeCompany();
    ['key' => $otherKey] = makeApiKey($otherCompany);

    $response = $this->withHeaders(['X-API-Key' => $otherKey])
        ->getJson('/api/v1/me')
        ->assertOk();

    expect($response->json('company.id'))->toBe($otherCompany->id);
});

it('GET /me returns 401 for expired API key', function () {
    $this->apiKey->update(['expires_at' => now()->subDay()]);

    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/me')
        ->assertUnauthorized();
});

it('GET /me accepts Bearer token format', function () {
    $this->withHeaders(['Authorization' => 'Bearer ' . $this->plainKey])
        ->getJson('/api/v1/me')
        ->assertOk();
});

// ---------- /modules endpoint ----------

it('GET /modules returns 401 without API key', function () {
    $this->getJson('/api/v1/modules')
        ->assertUnauthorized();
});

it('GET /modules returns enabled modules for the company', function () {
    attachModule($this->company, 'hr', 'hr');

    $response = $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/modules')
        ->assertOk();

    $keys = collect($response->json('modules'))->pluck('key')->all();
    expect($keys)->toContain('hr');
});

it('GET /modules does not return disabled modules', function () {
    $module = attachModule($this->company, 'hr', 'hr');
    // Disable the module
    $this->company->modules()->updateExistingPivot($module->id, ['is_enabled' => false]);

    $response = $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/modules')
        ->assertOk();

    $keys = collect($response->json('modules'))->pluck('key')->all();
    expect($keys)->not->toContain('hr');
});

// ---------- /hr/employees endpoint ----------

it('GET /hr/employees returns 401 without key', function () {
    $this->getJson('/api/v1/hr/employees')
        ->assertUnauthorized();
});

it('GET /hr/employees returns employees scoped to company', function () {
    Employee::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'first_name'  => 'Alice',
        'last_name'   => 'Smith',
        'email'       => 'alice@example.com',
        'start_date'  => now()->toDateString(),
    ]);

    $otherCompany = makeCompany();
    Employee::withoutGlobalScopes()->create([
        'company_id' => $otherCompany->id,
        'first_name' => 'Bob',
        'last_name'  => 'Jones',
        'email'      => 'bob@example.com',
        'start_date' => now()->toDateString(),
    ]);

    $response = $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/hr/employees')
        ->assertOk();

    $emails = collect($response->json('data'))->pluck('email')->all();
    expect($emails)->toContain('alice@example.com');
    expect($emails)->not->toContain('bob@example.com');
});

// ---------- /projects/tasks endpoint ----------

it('GET /projects/tasks returns 401 without key', function () {
    $this->getJson('/api/v1/projects/tasks')
        ->assertUnauthorized();
});

it('GET /projects/tasks returns tasks scoped to company', function () {
    Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'My Task',
        'status'     => 'todo',
        'priority'   => 'p3_medium',
    ]);

    $otherCompany = makeCompany();
    Task::withoutGlobalScopes()->create([
        'company_id' => $otherCompany->id,
        'title'      => 'Other Company Task',
        'status'     => 'todo',
        'priority'   => 'p3_medium',
    ]);

    $response = $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/projects/tasks')
        ->assertOk();

    $titles = collect($response->json('data'))->pluck('title')->all();
    expect($titles)->toContain('My Task');
    expect($titles)->not->toContain('Other Company Task');
});

it('api key last_used_at is updated on successful request', function () {
    $before = $this->apiKey->last_used_at;

    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/me');

    $this->apiKey->refresh();
    expect($this->apiKey->last_used_at)->not->toBeNull();
});

it('deleted API key returns 401', function () {
    $this->apiKey->delete();

    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/me')
        ->assertUnauthorized();
});
