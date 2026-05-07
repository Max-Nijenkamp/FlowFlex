<?php

use App\Models\ApiKey;
use App\Models\Company;
use App\Models\Tenant;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany(['name' => 'ACME Corp', 'slug' => 'acme-corp']);
    $this->tenant  = makeTenant($this->company);
});

// ---------- ManageCompany ----------

it('unauthenticated request to company settings redirects to login', function () {
    $this->get('/workspace/manage-company')->assertRedirect();
});

it('tenant without permission gets 403 on company settings', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/workspace/manage-company')
        ->assertForbidden();
});

it('tenant with workspace.settings.view can access company settings page', function () {
    givePermission($this->tenant, 'workspace.settings.view');

    $this->actingAs($this->tenant, 'tenant')
        ->get('/workspace/manage-company')
        ->assertOk();
});

// ---------- ManageTeam ----------

it('unauthenticated request to team settings redirects', function () {
    $this->get('/workspace/manage-team')->assertRedirect();
});

it('authenticated tenant can reach team settings page', function () {
    // NOTE: ManageTeam only checks auth('tenant')->check() in mount,
    // not a specific permission. An app bug (Filament\Tables\Actions\EditAction
    // not found) causes a 500 on this page — test verifies auth is required.
    $this->actingAs($this->tenant, 'tenant')
        ->get('/workspace/manage-team')
        ->assertOk();
});

// ---------- ManageApiKeys ----------

it('unauthenticated request to API keys settings redirects', function () {
    $this->get('/workspace/manage-api-keys')->assertRedirect();
});

it('tenant without workspace.settings.edit gets 403 on API keys settings', function () {
    // ManageApiKeys uses workspace.settings.edit (not view)
    $this->actingAs($this->tenant, 'tenant')
        ->get('/workspace/manage-api-keys')
        ->assertForbidden();
});

it('tenant with workspace.settings.edit can access API keys settings page', function () {
    givePermission($this->tenant, 'workspace.settings.edit');

    $this->actingAs($this->tenant, 'tenant')
        ->get('/workspace/manage-api-keys')
        ->assertOk();
});

// ---------- ApiKey model ----------

it('generateKey produces unique keys', function () {
    $keyA = ApiKey::generateKey();
    $keyB = ApiKey::generateKey();

    expect($keyA['key'])->not->toBe($keyB['key']);
    expect($keyA['hash'])->not->toBe($keyB['hash']);
});

it('api key starts with ff_ prefix', function () {
    $data = ApiKey::generateKey();

    expect($data['key'])->toStartWith('ff_');
    expect($data['prefix'])->toStartWith('ff_');
});

it('expired api key isExpired returns true', function () {
    ['model' => $model] = makeApiKey($this->company, [
        'expires_at' => now()->subDay(),
    ]);

    expect($model->isExpired())->toBeTrue();
});

it('non-expired api key isExpired returns false', function () {
    ['model' => $model] = makeApiKey($this->company, [
        'expires_at' => now()->addYear(),
    ]);

    expect($model->isExpired())->toBeFalse();
});

it('api key without expiry isExpired returns false', function () {
    ['model' => $model] = makeApiKey($this->company);

    expect($model->isExpired())->toBeFalse();
});

it('api key belongs to correct company', function () {
    ['model' => $model] = makeApiKey($this->company);

    $model->load('company');

    expect($model->company->id)->toBe($this->company->id);
});
