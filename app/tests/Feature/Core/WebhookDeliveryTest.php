<?php

declare(strict_types=1);

use App\Actions\Core\RotateWebhookSecretAction;
use App\Contracts\Core\BillingServiceInterface;
use App\Data\Core\ActivateModuleData;
use App\Data\Core\CreateWebhookEndpointData;
use App\Jobs\Core\DeliverWebhookJob;
use App\Models\Company;
use App\Models\Core\WebhookDelivery;
use App\Models\Core\WebhookEndpoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
});

it('fans out a subscribed event to the company endpoint with a valid signature', function () {
    config()->set('flowflex.modules', [
        'hr.payroll' => ['name' => 'Payroll', 'per_user_monthly_price_cents' => 100],
    ]);
    Http::fake(['example.com/*' => Http::response(['ok' => true], 200)]);

    $endpoint = WebhookEndpoint::factory()->forCompany($this->company)->events(['ModuleActivated'])->create();
    $user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($user, 'web');

    app(BillingServiceInterface::class)->activateModule(new ActivateModuleData('hr.payroll'));

    $delivery = WebhookDelivery::query()->firstOrFail();
    expect($delivery->event_type)->toBe('ModuleActivated')
        ->and($delivery->delivered_at)->not->toBeNull()
        ->and($delivery->response_status)->toBe(200);

    Http::assertSent(function ($request) use ($endpoint) {
        $expected = 'sha256='.hash_hmac('sha256', $request->body(), $endpoint->secret);

        return $request->url() === $endpoint->url
            && hash_equals($expected, $request->header('X-FlowFlex-Signature')[0]);
    });
});

it('never delivers company A events to company B endpoints', function () {
    config()->set('flowflex.modules', [
        'hr.payroll' => ['name' => 'Payroll', 'per_user_monthly_price_cents' => 100],
    ]);
    Http::fake();

    $other = Company::factory()->create();
    WebhookEndpoint::factory()->forCompany($other)->events(['ModuleActivated'])->create();

    $user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($user, 'web');
    app(BillingServiceInterface::class)->activateModule(new ActivateModuleData('hr.payroll'));

    expect(WebhookDelivery::query()->withoutGlobalScopes()->count())->toBe(0);
    Http::assertNothingSent();
});

it('records failures and auto-disables after the threshold', function () {
    Http::fake(['example.com/*' => Http::response('err', 500)]);

    $endpoint = WebhookEndpoint::factory()->forCompany($this->company)->create([
        'consecutive_failures' => 19,
    ]);
    $delivery = WebhookDelivery::create([
        'endpoint_id' => $endpoint->id,
        'company_id' => $this->company->id,
        'event_type' => 'webhook.test',
        'payload' => ['event' => 'webhook.test'],
    ]);

    try {
        (new DeliverWebhookJob($endpoint->id, 'webhook.test', ['event' => 'webhook.test'], $delivery->id))->handle();
    } catch (Throwable) {
        // release() outside a real queue context may throw — state assertions below are what matter
    }

    $fresh = $endpoint->fresh();
    expect($fresh->consecutive_failures)->toBe(20)
        ->and($fresh->is_active)->toBeFalse()
        ->and($delivery->fresh()->delivered_at)->toBeNull();
});

it('stores the secret encrypted and rotates to a new one', function () {
    $endpoint = WebhookEndpoint::factory()->forCompany($this->company)->create();
    $oldSecret = $endpoint->secret;

    $raw = DB::table('webhook_endpoints')->where('id', $endpoint->id)->value('secret');
    expect($raw)->not->toContain($oldSecret); // ciphertext at rest

    $new = RotateWebhookSecretAction::run($endpoint->id);
    expect($new)->toStartWith('whsec_')
        ->and($endpoint->fresh()->secret)->toBe($new)
        ->and($new)->not->toBe($oldSecret);
});

it('rejects non-HTTPS URLs at the DTO layer', function () {
    CreateWebhookEndpointData::validateAndCreate([
        'url' => 'http://insecure.example.com/hook',
        'events' => ['ModuleActivated'],
    ]);
})->throws(ValidationException::class);
