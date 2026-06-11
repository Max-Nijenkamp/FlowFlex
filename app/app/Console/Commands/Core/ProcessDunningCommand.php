<?php

declare(strict_types=1);

namespace App\Console\Commands\Core;

use App\Contracts\Core\BillingServiceInterface;
use App\Models\Core\BillingInvoice;
use App\States\Core\BillingInvoice\PastDue;
use Illuminate\Console\Command;

class ProcessDunningCommand extends Command
{
    protected $signature = 'flowflex:process-dunning';

    protected $description = 'Retry past-due invoices on the dunning schedule; suspend after exhaustion';

    public function handle(BillingServiceInterface $billing): int
    {
        $due = BillingInvoice::query()->withoutGlobalScopes()
            ->whereState('status', PastDue::class)
            ->whereNotNull('next_dunning_at')
            ->where('next_dunning_at', '<=', now())
            ->get();

        foreach ($due as $invoice) {
            // Retry = re-signal a failed payment cycle; Stripe retry happens on
            // Stripe's side, our webhook moves the state. Here we only advance
            // the schedule (or suspend on exhaustion).
            $billing->handleStripeWebhook([
                'type' => 'invoice.payment_failed',
                'data' => ['object' => ['id' => $invoice->stripe_invoice_id]],
            ]);
        }

        $this->info("Processed {$due->count()} past-due invoices.");

        return self::SUCCESS;
    }
}
