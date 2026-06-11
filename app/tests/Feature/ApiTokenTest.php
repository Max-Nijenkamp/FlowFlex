<?php

declare(strict_types=1);

use App\Actions\CreateApiTokenAction;
use App\Actions\RevokeApiTokenAction;
use App\Data\CreateApiTokenData;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');
});

it('creates a scoped token and returns the plain text once', function () {
    $plain = CreateApiTokenAction::run(new CreateApiTokenData('integration', ['hr:read']));

    expect($plain)->toContain('|');

    $token = $this->user->tokens()->firstOrFail();
    expect($token->abilities)->toBe(['hr:read'])
        ->and($token->created_by)->toBe($this->user->id)
        ->and($token->token)->not->toBe($plain); // stored hashed
});

it('authenticates API requests with the token', function () {
    $plain = CreateApiTokenAction::run(new CreateApiTokenData('integration', ['hr:read']));

    auth('web')->logout();
    Sanctum::$accessTokenAuthenticationCallback = null;

    $this->getJson('/api/v1/company', ['Authorization' => "Bearer {$plain}"])
        ->assertOk()
        ->assertJsonPath('data.id', $this->company->id);
});

it('rejects requests after revocation', function () {
    $plain = CreateApiTokenAction::run(new CreateApiTokenData('integration', ['hr:read']));
    $tokenId = (string) $this->user->tokens()->firstOrFail()->getKey();

    RevokeApiTokenAction::run($tokenId);
    auth('web')->logout();
    app('auth')->forgetGuards();

    $this->getJson('/api/v1/company', ['Authorization' => "Bearer {$plain}"])
        ->assertUnauthorized();
});

it('rejects malformed ability strings', function () {
    CreateApiTokenData::validateAndCreate(['name' => 'x', 'abilities' => ['hr:admin']]);
})->throws(ValidationException::class);

it('gates inactive-module API endpoints with 403', function () {
    Route::middleware(['auth:sanctum', 'module:hr.payroll'])
        ->get('/api/v1/_test/payroll', fn () => response()->json(['ok' => true]));

    $plain = CreateApiTokenAction::run(new CreateApiTokenData('integration', ['hr:read']));
    auth('web')->logout();

    $this->getJson('/api/v1/_test/payroll', ['Authorization' => "Bearer {$plain}"])
        ->assertForbidden();
});
