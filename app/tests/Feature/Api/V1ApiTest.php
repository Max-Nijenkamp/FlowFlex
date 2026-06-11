<?php

declare(strict_types=1);

use App\Actions\CreateApiTokenAction;
use App\Data\CreateApiTokenData;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\CRM\PipelineStage;
use App\Models\HR\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');

    foreach (['hr.profiles', 'hr.leave', 'finance.invoicing', 'crm.contacts', 'crm.deals'] as $key) {
        CompanyModuleSubscription::factory()->forCompany($this->company)->module($key)->create();
    }
});

function apiHeaders($test, array $abilities): array
{
    $plain = CreateApiTokenAction::run(new CreateApiTokenData('test', $abilities));
    auth('web')->logout();
    app('auth')->forgetGuards();

    return ['Authorization' => "Bearer {$plain}", 'Accept' => 'application/json'];
}

it('lists employees with pagination meta and DTO shape', function () {
    Employee::factory()->forCompany($this->company)->count(3)->create();

    $this->getJson('/api/v1/employees', apiHeaders($this, ['hr:read']))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'employee_number', 'full_name', 'email', 'job_title', 'status']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
});

it('creates an employee via API (hire path, fires events)', function () {
    $this->postJson('/api/v1/employees', [
        'first_name' => 'Api', 'last_name' => 'Hire', 'email' => 'api@acme.test',
        'hire_date' => '2026-06-01', 'job_title' => 'Dev', 'employment_type' => 'full-time',
    ], apiHeaders($this, ['hr:write']))
        ->assertCreated()
        ->assertJsonPath('data.employee_number', '1');
});

it('blocks writes with a read-only token (403)', function () {
    $this->postJson('/api/v1/employees', [
        'first_name' => 'No', 'last_name' => 'Write', 'email' => 'no@acme.test',
        'hire_date' => '2026-06-01', 'job_title' => 'Dev', 'employment_type' => 'full-time',
    ], apiHeaders($this, ['hr:read']))
        ->assertForbidden();
});

it('blocks inactive-module endpoints (deals without crm.deals... gated company)', function () {
    $other = Company::factory()->create();
    $otherUser = User::factory()->forCompany($other)->create();
    $this->actingAs($otherUser, 'web');
    $this->setCompany($other);
    $plain = CreateApiTokenAction::run(new CreateApiTokenData('other', ['crm:read']));
    auth('web')->logout();
    app('auth')->forgetGuards();

    $this->getJson('/api/v1/deals', ['Authorization' => "Bearer {$plain}"])
        ->assertForbidden(); // module not active for that company
});

it('tenant-isolates API reads via token context', function () {
    Employee::factory()->forCompany($this->company)->count(2)->create();
    $other = Company::factory()->create();
    Employee::factory()->forCompany($other)->count(5)->create();

    $this->getJson('/api/v1/employees', apiHeaders($this, ['hr:read']))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a deal via API', function () {
    $stage = PipelineStage::factory()->forCompany($this->company)->create();

    $this->postJson('/api/v1/deals', [
        'name' => 'API Deal', 'stage_id' => $stage->id, 'value_cents' => 99000,
    ], apiHeaders($this, ['crm:write']))
        ->assertCreated()
        ->assertJsonPath('data.value_cents', 99000);
});
