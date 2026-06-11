<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Contracts\CRM\DealServiceInterface;
use App\Events\CRM\DealLost;
use App\Events\CRM\DealStageMoved;
use App\Events\CRM\DealWon;
use App\Exceptions\CRM\ClosedDealImmutableException;
use App\Models\CRM\Deal;
use App\Models\CRM\PipelineStage;
use App\States\CRM\Deal\Lost;
use App\States\CRM\Deal\Open;
use App\States\CRM\Deal\Won;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class DealService implements DealServiceInterface
{
    public function create(
        string $name,
        string $stageId,
        int $valueCents,
        ?string $contactId = null,
        ?string $accountId = null,
        ?string $expectedCloseDate = null,
    ): Deal {
        $stage = PipelineStage::query()->findOrFail($stageId);

        return Deal::create([
            'name' => $name,
            'stage_id' => $stage->id,
            'value_cents' => $valueCents,
            'contact_id' => $contactId,
            'account_id' => $accountId,
            'owner_id' => request()->user()->id ?? Auth::guard('web')->id(),
            'probability' => $stage->probability_default,
            'expected_close_date' => $expectedCloseDate,
            'stage_entered_at' => now(),
        ])->refresh(); // pull DB defaults (currency, status)
    }

    public function moveToStage(string $dealId, string $stageId): Deal
    {
        $deal = Deal::query()->findOrFail($dealId);

        if (! $deal->status->equals(Open::class)) {
            throw new ClosedDealImmutableException('Closed deals cannot change stage — reopen first.');
        }

        $stage = PipelineStage::query()->findOrFail($stageId);

        $deal->update([
            'stage_id' => $stage->id,
            'probability' => $stage->probability_default,
            'stage_entered_at' => now(),
        ]);

        event(new DealStageMoved(
            company_id: $deal->company_id,
            deal_id: $deal->id,
            stage_id: $stage->id,
            moved_by: (string) Auth::guard('web')->id(),
        ));

        return $deal->refresh();
    }

    public function win(string $dealId): Deal
    {
        $deal = Deal::query()->findOrFail($dealId);

        $deal->status->transitionTo(Won::class);
        $deal->forceFill(['actual_close_date' => now(), 'probability' => 100, 'forecast_category' => 'closed'])->save();

        app(WinLossService::class)->record($deal->id, 'won', 'won'); // revenue-intelligence row (same-domain)

        event(new DealWon(
            company_id: $deal->company_id,
            deal_id: $deal->id,
            account_id: $deal->account_id,
            contact_id: $deal->contact_id,
            owner_id: $deal->owner_id,
            value_cents: $deal->value_cents,
            currency: $deal->currency,
            won_at: CarbonImmutable::now(),
        ));

        return $deal->refresh();
    }

    public function lose(string $dealId, string $reason): Deal
    {
        $deal = Deal::query()->findOrFail($dealId);

        $deal->status->transitionTo(Lost::class);
        $deal->forceFill(['actual_close_date' => now(), 'probability' => 0, 'lost_reason' => $reason])->save();

        app(WinLossService::class)->record($deal->id, 'lost', $reason); // revenue-intelligence row (same-domain)

        event(new DealLost(
            company_id: $deal->company_id,
            deal_id: $deal->id,
            owner_id: $deal->owner_id,
            lost_reason: $reason,
            lost_at: CarbonImmutable::now(),
        ));

        return $deal->refresh();
    }

    public function weightedPipelineValue(?string $ownerId = null): Money
    {
        $total = Money::ofMinor(0, 'EUR');

        Deal::query()
            ->where('status', 'open')
            ->when($ownerId !== null, fn ($q) => $q->where('owner_id', $ownerId))
            ->get(['value_cents', 'probability'])
            ->each(function (Deal $deal) use (&$total): void {
                $total = $total->plus(
                    Money::ofMinor($deal->value_cents, 'EUR')
                        ->multipliedBy((string) ($deal->probability / 100), RoundingMode::HALF_UP)
                );
            });

        return $total;
    }
}
