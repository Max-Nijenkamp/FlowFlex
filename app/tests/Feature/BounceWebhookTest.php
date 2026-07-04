<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\EmailSuppression;
use App\Models\User;

function signResendPayload(array $payload, string $secret): array
{
    $body = json_encode($payload);
    $id = 'msg_test';
    $timestamp = (string) time();
    $key = base64_decode(str_replace('whsec_', '', $secret), true);
    $signature = base64_encode(hash_hmac('sha256', "{$id}.{$timestamp}.{$body}", $key, true));

    return [$body, [
        'svix-id' => $id,
        'svix-timestamp' => $timestamp,
        'svix-signature' => "v1,{$signature}",
        'Content-Type' => 'application/json',
    ]];
}

beforeEach(function () {
    config()->set('services.resend.webhook_secret', 'whsec_'.base64_encode('test-secret-key'));
});

test('a validly signed hard bounce flags the user undeliverable', function () {
    $user = User::factory()->for(Company::factory())->create(['email_deliverable' => true]);

    [$body, $headers] = signResendPayload([
        'type' => 'email.bounced',
        'data' => ['to' => [$user->email]],
    ], config('services.resend.webhook_secret'));

    $this->call('POST', '/api/resend/webhook', [], [], [], $this->transformHeadersToServerVars($headers), $body)
        ->assertOk();

    expect($user->fresh()->email_deliverable)->toBeFalse();
});

test('an invalid signature is rejected and nothing changes', function () {
    $user = User::factory()->for(Company::factory())->create(['email_deliverable' => true]);

    $this->postJson('/api/resend/webhook', [
        'type' => 'email.bounced',
        'data' => ['to' => [$user->email]],
    ], ['svix-id' => 'x', 'svix-timestamp' => '1', 'svix-signature' => 'v1,garbage'])
        ->assertForbidden();

    expect($user->fresh()->email_deliverable)->toBeTrue();
});

test('a spam complaint suppresses the address immediately', function () {
    [$body, $headers] = signResendPayload([
        'type' => 'email.complained',
        'data' => ['to' => ['angry@customer.nl']],
    ], config('services.resend.webhook_secret'));

    $this->call('POST', '/api/resend/webhook', [], [], [], $this->transformHeadersToServerVars($headers), $body)
        ->assertOk();

    $row = EmailSuppression::query()->where('email', 'angry@customer.nl')->first();
    expect($row)->not->toBeNull()
        ->and($row->reason)->toBe('complaint')
        ->and($row->suppressed_at)->not->toBeNull();
});

test('soft bounces only suppress after the third delivery delay', function () {
    $payload = ['type' => 'email.delivery_delayed', 'data' => ['to' => ['greylisted@host.nl']]];

    foreach (range(1, 3) as $attempt) {
        [$body, $headers] = signResendPayload($payload, config('services.resend.webhook_secret'));
        $this->call('POST', '/api/resend/webhook', [], [], [], $this->transformHeadersToServerVars($headers), $body)
            ->assertOk();

        $row = EmailSuppression::query()->where('email', 'greylisted@host.nl')->firstOrFail();
        expect($row->suppressed_at !== null)->toBe($attempt >= 3, "attempt {$attempt}");
    }
});

test('a hard bounce lands on the suppression list and flags the user', function () {
    $user = User::factory()->for(Company::factory())->create(['email_deliverable' => true]);

    [$body, $headers] = signResendPayload([
        'type' => 'email.bounced',
        'data' => ['to' => [$user->email]],
    ], config('services.resend.webhook_secret'));

    $this->call('POST', '/api/resend/webhook', [], [], [], $this->transformHeadersToServerVars($headers), $body)
        ->assertOk();

    expect($user->fresh()->email_deliverable)->toBeFalse()
        ->and(EmailSuppression::query()->where('email', $user->email)->whereNotNull('suppressed_at')->exists())->toBeTrue();
});

test('non-bounce events are acknowledged without side effects', function () {
    $user = User::factory()->for(Company::factory())->create(['email_deliverable' => true]);

    [$body, $headers] = signResendPayload([
        'type' => 'email.delivered',
        'data' => ['to' => [$user->email]],
    ], config('services.resend.webhook_secret'));

    $this->call('POST', '/api/resend/webhook', [], [], [], $this->transformHeadersToServerVars($headers), $body)
        ->assertOk();

    expect($user->fresh()->email_deliverable)->toBeTrue();
});
