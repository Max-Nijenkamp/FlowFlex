<?php

declare(strict_types=1);

use App\Contracts\BillingServiceInterface;
use App\Events\CompanySubscriptionSuspended;
use App\Models\BillingInvoice;
use App\Models\Company;
use App\Models\User;
use App\States\BillingInvoice\Open;
use App\States\BillingInvoice\Paid;
use App\States\BillingInvoice\PastDue;
use App\States\BillingInvoice\Uncollectible;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

function openInvoice(Company $company): BillingInvoice
{
    $invoice = BillingInvoice::factory()->forCompany($company)->create([
        'stripe_invoice_id' => 'in_'.fake()->unique()->lexify('????????'),
    ]);
    $invoice->status->transitionTo(Open::class);

    return $invoice->refresh();
}

function failPayment(BillingInvoice $invoice): void
{
    app(BillingServiceInterface::class)->handleStripeWebhook([
        'type' => 'invoice.payment_failed',
        'data' => ['object' => ['id' => $invoice->stripe_invoice_id]],
    ]);
}

it('marks an invoice paid on payment_succeeded', function () {
    $company = Company::factory()->create();
    $invoice = openInvoice($company);

    app(BillingServiceInterface::class)->handleStripeWebhook([
        'type' => 'invoice.payment_succeeded',
        'data' => ['object' => ['id' => $invoice->stripe_invoice_id]],
    ]);

    $fresh = $invoice->fresh();
    expect($fresh->status->equals(Paid::class))->toBeTrue()
        ->and($fresh->paid_at)->not->toBeNull();
});

it('starts the dunning schedule on payment_failed', function () {
    $company = Company::factory()->create();
    $invoice = openInvoice($company);

    failPayment($invoice);

    $fresh = $invoice->fresh();
    expect($fresh->status->equals(PastDue::class))->toBeTrue()
        ->and($fresh->dunning_attempts)->toBe(1)
        ->and($fresh->next_dunning_at)->not->toBeNull();
});

it('suspends the company after dunning exhausts', function () {
    Event::fake([CompanySubscriptionSuspended::class]);
    $company = Company::factory()->create();
    $invoice = openInvoice($company);

    foreach (range(1, 4) as $attempt) {
        failPayment($invoice->fresh());
    }

    expect($invoice->fresh()->status->equals(Uncollectible::class))->toBeTrue()
        ->and($company->fresh()->subscription_status)->toBe('suspended');

    Event::assertDispatched(CompanySubscriptionSuspended::class, fn ($e) => $e->company_id === $company->id);
});

it('blocks suspended companies from the app panel', function () {
    $company = Company::factory()->create(['subscription_status' => 'suspended']);
    $user = User::factory()->forCompany($company)->create();

    $this->actingAs($user, 'web')->get('/app')->assertStatus(402);
});

it('rejects an unsigned stripe webhook with 400', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test');

    $this->postJson('/api/stripe/webhook', ['type' => 'invoice.payment_succeeded'])
        ->assertStatus(400);
});

it('ignores webhooks for unknown invoices without error', function () {
    app(BillingServiceInterface::class)->handleStripeWebhook([
        'type' => 'invoice.payment_succeeded',
        'data' => ['object' => ['id' => 'in_unknown']],
    ]);

    expect(true)->toBeTrue();
});
