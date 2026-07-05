<?php

declare(strict_types=1);

namespace App\Console\Commands\Finance;

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Models\Finance\Invoice;
use App\Models\Finance\InvoiceLine;
use App\Services\Finance\InvoiceService;
use App\Support\Jobs\Middleware\WithCompanyContext;
use App\Support\Services\CompanyContext;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Daily recurring-invoice run (finance.invoicing/recurring-invoices).
 * Idempotent: WHERE next_recurring_at <= today, and the date advances
 * in the same transaction as the copy — a re-run generates nothing new.
 */
class GenerateRecurringInvoicesCommand extends Command
{
    protected $signature = 'finance:generate-recurring-invoices';

    protected $description = 'Generate the next invoice for every due recurring schedule';

    public function handle(): int
    {
        /** @var Collection<int, Invoice> $due */
        $due = Invoice::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereNotNull('recurring_schedule')
            ->whereNotNull('next_recurring_at')
            ->whereDate('next_recurring_at', '<=', now())
            ->get();

        $generated = 0;

        foreach ($due as $template) {
            WithCompanyContext::restore($template->company_id);

            DB::transaction(function () use ($template, &$generated): void {
                /** @var Collection<int, InvoiceLine> $lines */
                $lines = $template->lines()->get();

                app(InvoiceServiceInterface::class)->create(new CreateInvoiceData(
                    customerId: $template->customer_id,
                    lines: $lines->map(fn (InvoiceLine $line): array => [
                        'description' => $line->description,
                        'quantity' => (string) $line->quantity,
                        'unit_price_cents' => $line->unit_price_cents,
                        'tax_rate_percent' => (float) $line->tax_rate_percent,
                    ])->all(),
                    discountPercent: (float) $template->discount_percent,
                    notes: $template->notes,
                ));

                // Advance inside the same transaction — the idempotency anchor.
                $template->update([
                    'next_recurring_at' => InvoiceService::nextRecurringDate(
                        $template->next_recurring_at->toImmutable(),
                        (string) $template->recurring_schedule,
                    ),
                ]);

                $generated++;
            });
        }

        app(CompanyContext::class)->forget();

        $this->info("Recurring invoices generated: {$generated}");

        return self::SUCCESS;
    }
}
