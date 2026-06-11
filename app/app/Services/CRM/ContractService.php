<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\Contract;
use App\Models\CRM\Deal;
use App\States\CRM\Contract\Active;
use App\States\CRM\Contract\Expired;
use App\States\CRM\Contract\Sent;
use App\States\CRM\Contract\Signed;
use App\States\CRM\Contract\Terminated;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;

class ContractService
{
    /** Prefills account/value from the won deal. */
    public function createFromDeal(string $dealId, string $billingInterval = 'one-off'): Contract
    {
        $deal = Deal::query()->findOrFail($dealId);

        return Contract::create([
            'account_id' => $deal->account_id,
            'deal_id' => $deal->id,
            'title' => "Contract — {$deal->name}",
            'value_cents' => $deal->value_cents,
            'currency' => $deal->currency,
            'billing_interval' => $billingInterval,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'renewal_date' => now()->addYear()->toDateString(),
        ])->refresh();
    }

    public function send(string $contractId): Contract
    {
        $contract = Contract::query()->findOrFail($contractId);
        $contract->status->transitionTo(Sent::class);

        return $contract->refresh();
    }

    /** v1 e-sign = manual signed-PDF upload + signed flag (per ADR'd assumption). */
    public function markSigned(string $contractId, UploadedFile $signedPdf): Contract
    {
        $contract = Contract::query()->findOrFail($contractId);

        $path = $signedPdf->storeAs(
            "companies/{$contract->company_id}/contracts",
            "{$contract->id}-signed.pdf",
        );

        $contract->status->transitionTo(Signed::class);
        $contract->update(['signed_at' => now(), 'signed_pdf_path' => $path]);
        $contract->status->transitionTo(Active::class);

        return $contract->refresh();
    }

    public function renew(string $contractId, CarbonImmutable $newEnd): Contract
    {
        $contract = Contract::query()->findOrFail($contractId);
        $contract->update([
            'end_date' => $newEnd->toDateString(),
            'renewal_date' => $newEnd->toDateString(),
            'alerted_levels' => [],
        ]);

        return $contract->refresh();
    }

    public function terminate(string $contractId, string $reason): Contract
    {
        $contract = Contract::query()->findOrFail($contractId);
        $contract->status->transitionTo(Terminated::class);
        activity()->performedOn($contract)->withProperties(['reason' => $reason])->log('contract.terminated');

        return $contract->refresh();
    }

    /**
     * Lifecycle sweep: expire passed contracts (auto-renew extends instead),
     * fire 90/30-day renewal alerts once per level.
     *
     * @return array{expired: int, renewed: int, alerted: int}
     */
    public function runLifecycle(): array
    {
        $result = ['expired' => 0, 'renewed' => 0, 'alerted' => 0];
        $today = CarbonImmutable::today();

        foreach (Contract::query()->whereState('status', Active::class)->get() as $contract) {
            $end = CarbonImmutable::parse($contract->end_date->toDateString());

            if ($end->isPast()) {
                if ($contract->auto_renew) {
                    $this->renew($contract->id, $end->addYear());
                    $result['renewed']++;
                } else {
                    $contract->status->transitionTo(Expired::class);
                    $result['expired']++;
                }

                continue;
            }

            foreach ([90, 30] as $level) {
                $alerted = $contract->alerted_levels ?? [];
                if (! in_array($level, $alerted, true) && $today->diffInDays($end) <= $level) {
                    $contract->update(['alerted_levels' => [...$alerted, $level]]);
                    $result['alerted']++; // notification record lands with email pass
                }
            }
        }

        return $result;
    }

    /** Active recurring contracts normalised to monthly (brick/money). */
    public function recurringRevenue(): Money
    {
        $monthly = Money::ofMinor(0, 'EUR');

        foreach (Contract::query()->whereState('status', Active::class)->where('billing_interval', '!=', 'one-off')->get() as $contract) {
            $value = Money::ofMinor($contract->value_cents, $contract->currency);
            $monthly = $monthly->plus(
                $contract->billing_interval === 'yearly'
                    ? $value->dividedBy(12, RoundingMode::HALF_UP)
                    : $value,
            );
        }

        return $monthly;
    }
}
