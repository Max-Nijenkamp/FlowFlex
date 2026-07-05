<?php

declare(strict_types=1);

namespace App\Console\Commands\Finance;

use App\Models\Finance\Invoice;
use App\States\Finance\Invoice\Overdue;
use App\Support\Services\CompanyContext;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

/**
 * Daily overdue sweep (finance.invoicing): sent/partially_paid invoices
 * past their due date flip to overdue. Re-runs touch nothing twice —
 * the state filter excludes already-overdue rows.
 */
class MarkOverdueInvoicesCommand extends Command
{
    protected $signature = 'finance:mark-overdue-invoices';

    protected $description = 'Flip past-due sent invoices to overdue';

    public function handle(): int
    {
        /** @var Collection<int, Invoice> $due */
        $due = Invoice::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereIn('status', ['sent', 'partially_paid'])
            ->whereDate('due_date', '<', now())
            ->get();

        foreach ($due as $invoice) {
            $invoice->status->transitionTo(Overdue::class);
        }

        app(CompanyContext::class)->forget();

        $this->info("Invoices marked overdue: {$due->count()}");

        return self::SUCCESS;
    }
}
