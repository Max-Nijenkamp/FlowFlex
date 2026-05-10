<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Core\BillingSubscription;
use Illuminate\Support\Facades\Config;

describe('Stripe Webhook Controller', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->subscription = BillingSubscription::create([
            'company_id'             => $this->company->id,
            'stripe_subscription_id' => 'sub_test123',
            'status'                 => 'trialing',
        ]);

        // Ensure no webhook secret configured (local dev mode)
        Config::set('services.stripe.webhook_secret', null);
    });

    it('returns 200 for unknown event type', function () {
        $this->postJson('/stripe/webhook', [
            'type' => 'unknown.event',
            'data' => ['object' => []],
        ])->assertOk()->assertJson(['received' => true]);
    });

    it('sets subscription active on invoice.payment_succeeded', function () {
        $this->subscription->update(['status' => 'past_due']);

        $this->postJson('/stripe/webhook', [
            'type' => 'invoice.payment_succeeded',
            'data' => ['object' => ['subscription' => 'sub_test123']],
        ])->assertOk();

        expect($this->subscription->fresh()->status)->toBe('active');
    });

    it('sets subscription past_due on invoice.payment_failed', function () {
        $this->subscription->update(['status' => 'active']);

        $this->postJson('/stripe/webhook', [
            'type' => 'invoice.payment_failed',
            'data' => ['object' => ['subscription' => 'sub_test123']],
        ])->assertOk();

        expect($this->subscription->fresh()->status)->toBe('past_due');
    });

    it('updates subscription status on customer.subscription.updated', function () {
        $this->postJson('/stripe/webhook', [
            'type' => 'customer.subscription.updated',
            'data' => ['object' => ['id' => 'sub_test123', 'status' => 'active']],
        ])->assertOk();

        expect($this->subscription->fresh()->status)->toBe('active');
    });

    it('cancels subscription on customer.subscription.deleted', function () {
        $this->subscription->update(['status' => 'active']);

        $this->postJson('/stripe/webhook', [
            'type' => 'customer.subscription.deleted',
            'data' => ['object' => ['id' => 'sub_test123']],
        ])->assertOk();

        $fresh = $this->subscription->fresh();
        expect($fresh->status)->toBe('canceled');
        expect($fresh->ends_at)->not->toBeNull();
    });

    it('ignores event when stripe subscription id not found', function () {
        $this->postJson('/stripe/webhook', [
            'type' => 'invoice.payment_succeeded',
            'data' => ['object' => ['subscription' => 'sub_nonexistent']],
        ])->assertOk();

        expect($this->subscription->fresh()->status)->toBe('trialing');
    });

    it('returns 400 when webhook secret configured and signature missing', function () {
        Config::set('services.stripe.webhook_secret', 'whsec_test');

        $this->postJson('/stripe/webhook', [
            'type' => 'invoice.payment_succeeded',
            'data' => ['object' => ['subscription' => 'sub_test123']],
        ])->assertStatus(400);
    });

    it('skips signature check when webhook secret not configured', function () {
        Config::set('services.stripe.webhook_secret', null);

        $this->postJson('/stripe/webhook', [
            'type' => 'invoice.payment_succeeded',
            'data' => ['object' => ['subscription' => 'sub_test123']],
        ])->assertOk();
    });
});
