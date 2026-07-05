<?php

declare(strict_types=1);

namespace App\Listeners\Finance;

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Events\Crm\DealWon;
use App\Models\Crm\Account;
use App\Models\Crm\Deal;
use App\Models\Crm\DealProduct;
use App\Models\Finance\Customer;
use App\Services\BillingService;
use App\Support\Jobs\Middleware\WithCompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

/**
 * DealWon → draft invoice stub (finance.invoicing, event-bus contract).
 * Lines copy from the deal's products (fallback: one line for the deal
 * value); the draft is never auto-sent. No-op while finance.invoicing
 * is inactive for the company.
 */
class CreateInvoiceStubListener implements ShouldQueue
{
    public string $queue = 'domain-events';

    public function handle(DealWon $event): void
    {
        WithCompanyContext::restore($event->company_id);

        if (! app(BillingService::class)->hasModule('finance.invoicing')) {
            return;
        }

        $customer = $this->resolveCustomer($event);

        $deal = Deal::query()->with('products')->find($event->deal_id);

        /** @var list<array{description: string, quantity: float|string, unit_price_cents: int}> $lines */
        $lines = [];

        if ($deal instanceof Deal) {
            /** @var Collection<int, DealProduct> $products */
            $products = $deal->products()->get();

            foreach ($products as $product) {
                $lines[] = [
                    'description' => $product->description,
                    'quantity' => (string) $product->quantity,
                    'unit_price_cents' => $product->unit_price_cents,
                ];
            }
        }

        if ($lines === []) {
            $lines[] = [
                'description' => $event->name,
                'quantity' => 1,
                'unit_price_cents' => $event->value_cents,
            ];
        }

        app(InvoiceServiceInterface::class)->create(new CreateInvoiceData(
            customerId: $customer->id,
            lines: $lines,
            sourceDealId: $event->deal_id,
        ));
    }

    private function resolveCustomer(DealWon $event): Customer
    {
        if ($event->account_id !== null) {
            $existing = Customer::query()->where('crm_account_id', $event->account_id)->first();

            if ($existing instanceof Customer) {
                return $existing;
            }

            $account = Account::query()->find($event->account_id);

            if ($account instanceof Account) {
                return Customer::query()->create([
                    'company_id' => $event->company_id,
                    'name' => $account->name,
                    'email' => 'billing@'.str($account->name)->slug()->toString().'.invalid',
                    'crm_account_id' => $account->id,
                ]);
            }
        }

        return Customer::query()->firstOrCreate(
            ['company_id' => $event->company_id, 'name' => $event->name.' (deal)'],
            ['email' => 'billing@unknown.invalid'],
        );
    }
}
