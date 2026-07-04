<?php

declare(strict_types=1);

namespace App\Services\Stripe;

use App\Models\BillingInvoice;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

/**
 * Thin raw-SDK wrapper (ADR: stripe/stripe-php, not Cashier). Every call
 * no-ops when no secret is configured (local/dev/CI) so billing flows stay
 * testable without touching the network; Http::preventStrayRequests()
 * enforces that in the suite.
 */
class StripeBillingClient
{
    public function enabled(): bool
    {
        return filled(config('services.stripe.secret'));
    }

    protected function client(): StripeClient
    {
        return new StripeClient((string) config('services.stripe.secret'));
    }

    public function ensureCustomer(Company $company): ?string
    {
        if (! $this->enabled()) {
            return $company->stripe_customer_id;
        }

        if ($company->stripe_customer_id !== null) {
            return $company->stripe_customer_id;
        }

        $customer = $this->client()->customers->create([
            'name' => $company->name,
            'metadata' => ['company_id' => $company->id],
        ]);

        $company->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }

    public function syncSubscriptionItem(Company $company, string $moduleKey, bool $active): void
    {
        if (! $this->enabled()) {
            Log::debug('Stripe disabled — skipped subscription item sync', [
                'company_id' => $company->id, 'module_key' => $moduleKey, 'active' => $active,
            ]);

            return;
        }

        $this->ensureCustomer($company);
        // Subscription-item bookkeeping happens on the monthly invoice in the
        // per-user model — nothing recurring to sync per activation.
    }

    /** @return string|null the Stripe invoice id */
    public function createInvoice(BillingInvoice $invoice): ?string
    {
        if (! $this->enabled()) {
            return null;
        }

        $company = $invoice->company()->withoutGlobalScopes()->firstOrFail();
        $customerId = $this->ensureCustomer($company);

        $stripeInvoice = $this->client()->invoices->create([
            'customer' => (string) $customerId,
            'currency' => strtolower($invoice->currency),
            'auto_advance' => true,
            'metadata' => ['billing_invoice_id' => $invoice->id, 'company_id' => $invoice->company_id],
        ]);

        $this->client()->invoiceItems->create([
            'customer' => (string) $customerId,
            'invoice' => $stripeInvoice->id,
            'amount' => $invoice->total_cents,
            'currency' => strtolower($invoice->currency),
            'description' => 'FlowFlex modules '.$invoice->period_start->format('M Y'),
        ]);

        return $stripeInvoice->id;
    }

    public function retryPayment(BillingInvoice $invoice): bool
    {
        if (! $this->enabled() || $invoice->stripe_invoice_id === null) {
            return false; // outcome arrives via webhook when live
        }

        $this->client()->invoices->pay($invoice->stripe_invoice_id);

        return true;
    }
}
