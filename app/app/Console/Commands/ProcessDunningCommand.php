<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\BillingServiceInterface;
use App\Models\BillingInvoice;
use App\Services\Stripe\StripeBillingClient;
use App\States\Core\BillingInvoice\PastDue;
use App\States\Core\BillingInvoice\Uncollectible;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Daily 06:00 dunning sweep (core.billing-engine/dunning): 3 retries over
 * 14 days; exhaustion moves the invoice to uncollectible and suspends the
 * company. WHERE-guards on next_retry_at keep re-runs idempotent.
 */
class ProcessDunningCommand extends Command
{
    public const MAX_ATTEMPTS = 3;

    /** Days between retries: attempt n schedules the next at these offsets. */
    public const RETRY_OFFSETS_DAYS = [5, 5, 0];

    protected $signature = 'billing:process-dunning';

    protected $description = 'Retry past-due invoices and suspend after exhaustion';

    public function handle(BillingServiceInterface $billing, StripeBillingClient $stripe): int
    {
        $due = BillingInvoice::query()
            ->withoutGlobalScopes()
            ->where('status', 'past_due')
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', now())
            ->get();

        foreach ($due as $invoice) {
            DB::transaction(function () use ($billing, $stripe, $invoice): void {
                /** @var BillingInvoice $locked */
                $locked = BillingInvoice::query()
                    ->withoutGlobalScopes()
                    ->whereKey($invoice->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $locked->status->equals(PastDue::class) || $locked->next_retry_at === null || $locked->next_retry_at->isFuture()) {
                    return; // state moved under us — idempotent skip
                }

                $attempt = $locked->dunning_attempts + 1;
                $stripe->retryPayment($locked); // outcome lands via webhook when live

                if ($attempt >= self::MAX_ATTEMPTS) {
                    $locked->status->transitionTo(Uncollectible::class);
                    $locked->update(['dunning_attempts' => $attempt, 'next_retry_at' => null]);

                    $billing->suspend($locked->company_id, 'invoice '.$locked->period_start->format('M Y').' uncollectible after dunning');

                    return;
                }

                $locked->update([
                    'dunning_attempts' => $attempt,
                    'next_retry_at' => now()->addDays(self::RETRY_OFFSETS_DAYS[$attempt - 1]),
                ]);
            });
        }

        $this->info("Processed {$due->count()} past-due invoices.");

        return self::SUCCESS;
    }
}
