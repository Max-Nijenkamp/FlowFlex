<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use App\Support\Scopes\CompanyScope;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/** Build valid Svix headers for a payload + raw secret key. */
function svixHeaders(string $payload, string $rawKey): array
{
    $id = 'msg_test';
    $ts = (string) 1700000000;
    $signed = "{$id}.{$ts}.{$payload}";
    $sig = base64_encode(hash_hmac('sha256', $signed, $rawKey, true));

    return [
        'svix-id' => $id,
        'svix-timestamp' => $ts,
        'svix-signature' => "v1,{$sig}",
    ];
}

beforeEach(function () {
    $this->rawKey = random_bytes(24);
    config()->set('services.resend.webhook_secret', 'whsec_'.base64_encode($this->rawKey));
});

it('flags email_deliverable false on a valid hard-bounce webhook', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create(['email' => 'bounce@example.com']);

    $payload = json_encode([
        'type' => 'email.bounced',
        'data' => ['to' => ['bounce@example.com'], 'bounce' => ['type' => 'hard']],
    ]);

    $this->call('POST', '/api/resend/webhook', [], [], [], array_merge(
        ['CONTENT_TYPE' => 'application/json'],
        collect(svixHeaders($payload, $this->rawKey))->mapWithKeys(fn ($v, $k) => ['HTTP_'.strtoupper(str_replace('-', '_', $k)) => $v])->all()
    ), $payload)->assertOk();

    expect(User::withoutGlobalScope(CompanyScope::class)->find($user->id)->email_deliverable)->toBeFalse();
});

it('rejects an invalid signature with 403 and changes nothing', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create(['email' => 'keep@example.com']);

    $payload = json_encode(['type' => 'email.bounced', 'data' => ['to' => ['keep@example.com'], 'bounce' => ['type' => 'hard']]]);

    $this->call('POST', '/api/resend/webhook', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_SVIX_ID' => 'msg_x',
        'HTTP_SVIX_TIMESTAMP' => '1700000000',
        'HTTP_SVIX_SIGNATURE' => 'v1,deadbeef',
    ], $payload)->assertForbidden();

    expect(User::withoutGlobalScope(CompanyScope::class)->find($user->id)->email_deliverable)->toBeTrue();
});
