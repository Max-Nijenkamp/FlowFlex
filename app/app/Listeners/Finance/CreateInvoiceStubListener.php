<?php

declare(strict_types=1);

namespace App\Listeners\Finance;

use App\Contracts\BillingServiceInterface;
use App\Contracts\Finance\InvoiceServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Events\CRM\DealWon;
use App\Models\Company;
use App\Models\CRM\Deal;
use App\Models\Finance\Customer;
use App\Support\Services\CompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateInvoiceStubListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(DealWon $event): void
    {
        $company = Company::query()->withoutGlobalScopes()->findOrFail($event->company_id);
        app(CompanyContext::class)->set($company);
        setPermissionsTeamId($company->id);

        // No-op when invoicing module inactive (event-bus contract).
        if (! app(BillingServiceInterface::class)->hasModule('finance.invoicing')) {
            return;
        }

        $deal = Deal::query()->withoutGlobalScopes()->findOrFail($event->deal_id);

        // Find-or-create the invoice customer from the deal's contact.
        $customer = Customer::query()->firstOrCreate(
            ['crm_account_id' => $event->account_id ?? $event->deal_id],
            [
                'name' => $deal->name,
                'email' => $deal->contact->email ?? 'unknown@example.invalid',
            ],
        );

        // Draft invoice — fallback single line at deal value; NEVER auto-sent.
        app(InvoiceServiceInterface::class)->create(new CreateInvoiceData(
            customer_id: $customer->id,
            issue_date: now()->toDateString(),
            lines: [[
                'description' => "Deal: {$deal->name}",
                'quantity' => 1,
                'unit_price_cents' => $event->value_cents,
            ]],
            source_deal_id: $event->deal_id,
        ));
    }
}
