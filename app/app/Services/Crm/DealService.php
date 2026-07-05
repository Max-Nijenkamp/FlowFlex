<?php

declare(strict_types=1);

namespace App\Services\Crm;

use App\Contracts\Crm\DealServiceInterface;
use App\Data\Crm\CloseDealData;
use App\Data\Crm\CreateDealData;
use App\Events\Crm\DealLost;
use App\Events\Crm\DealStageChanged;
use App\Events\Crm\DealWon;
use App\Exceptions\Crm\ClosedDealImmutableException;
use App\Models\Crm\Deal;
use App\Models\Crm\DealContact;
use App\Models\Crm\DealProduct;
use App\Models\Crm\PipelineStage;
use App\Models\User;
use App\States\Crm\Deal\Lost;
use App\States\Crm\Deal\Won;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Owns crm_deals writes (crm.deals). Every stage move — form, board
 * drag, API — funnels through moveToStage so the closed-deal guard,
 * probability default and stage_entered_at reset apply everywhere.
 */
class DealService implements DealServiceInterface
{
    public function create(CreateDealData $data): Deal
    {
        /** @var PipelineStage $stage */
        $stage = PipelineStage::query()->findOrFail($data->stageId);

        return Deal::query()->create([
            'company_id' => app(CompanyContext::class)->current()->id,
            'name' => $data->name,
            'account_id' => $data->accountId,
            'contact_id' => $data->contactId,
            'owner_id' => $data->ownerId ?? Auth::id(),
            'stage_id' => $stage->id,
            'value_cents' => $data->valueCents,
            'currency' => $data->currency,
            'probability' => $stage->probability_default,
            'expected_close_date' => $data->expectedCloseDate,
            'stage_entered_at' => now(),
        ]);
    }

    public function moveToStage(string $dealId, string $stageId): Deal
    {
        return DB::transaction(function () use ($dealId, $stageId): Deal {
            /** @var Deal $deal */
            $deal = Deal::query()->whereKey($dealId)->lockForUpdate()->firstOrFail();

            if ($deal->isClosed()) {
                throw ClosedDealImmutableException::make();
            }

            /** @var PipelineStage $stage */
            $stage = PipelineStage::query()->findOrFail($stageId);

            $fromStageId = $deal->stage_id;

            $deal->update([
                'stage_id' => $stage->id,
                'probability' => $stage->probability_default,
                'stage_entered_at' => now(),
            ]);

            // Won/lost columns on the board close the deal through the
            // same drag gesture.
            if ($stage->is_won || $stage->is_lost) {
                $deal = $this->close(new CloseDealData(
                    dealId: $deal->id,
                    outcome: $stage->is_won ? 'won' : 'lost',
                    lostReason: $stage->is_lost ? 'Moved to lost stage on the board' : null,
                ));
            }

            DealStageChanged::dispatch(
                $deal->company_id,
                $deal->id,
                $fromStageId,
                $stage->id,
                (string) Auth::id(),
            );

            return $deal;
        });
    }

    public function close(CloseDealData $data): Deal
    {
        /** @var Deal $deal */
        $deal = Deal::query()->findOrFail($data->dealId);

        if ($deal->isClosed()) {
            throw ClosedDealImmutableException::make();
        }

        if ($data->outcome === 'lost' && ($data->lostReason === null || $data->lostReason === '')) {
            throw ValidationException::withMessages(['lost_reason' => 'A lost reason is required.']);
        }

        $deal->status->transitionTo($data->outcome === 'won' ? Won::class : Lost::class);
        $deal->update([
            'actual_close_date' => now()->toDateString(),
            'lost_reason' => $data->outcome === 'lost' ? $data->lostReason : null,
            'lost_to' => $data->outcome === 'lost' ? $data->lostTo : null,
        ]);

        $causer = Auth::user();
        app(AuditLogger::class)->log(
            'crm.deal-closed',
            $deal,
            $causer instanceof User ? $causer : null,
            ['outcome' => $data->outcome, 'value_cents' => $deal->value_cents],
        );

        if ($data->outcome === 'won') {
            DealWon::dispatch(
                $deal->company_id, $deal->id, $deal->account_id, $deal->contact_id,
                $deal->value_cents, $deal->currency, $deal->name,
            );
        } else {
            DealLost::dispatch($deal->company_id, $deal->id, (string) $data->lostReason, $data->lostTo);
        }

        return $deal->refresh();
    }

    /** Copy a deal to start a new cycle: contacts + products, fresh status/stage. */
    public function duplicate(string $dealId): Deal
    {
        /** @var Deal $source */
        $source = Deal::query()->with(['dealContacts', 'products'])->findOrFail($dealId);
        /** @var Collection<int, DealContact> $sourceContacts */
        $sourceContacts = $source->dealContacts()->get();
        /** @var Collection<int, DealProduct> $sourceProducts */
        $sourceProducts = $source->products()->get();

        $firstOpenStage = PipelineStage::query()
            ->where('is_won', false)->where('is_lost', false)
            ->orderBy('order')
            ->firstOrFail();

        /** @var Deal $copy */
        $copy = Deal::query()->create([
            'company_id' => $source->company_id,
            'name' => $source->name.' (copy)',
            'account_id' => $source->account_id,
            'contact_id' => $source->contact_id,
            'owner_id' => Auth::id() ?? $source->owner_id,
            'stage_id' => $firstOpenStage->id,
            'value_cents' => $source->value_cents,
            'currency' => $source->currency,
            'probability' => $firstOpenStage->probability_default,
            'expected_close_date' => null,
            'stage_entered_at' => now(),
        ]);

        foreach ($sourceContacts as $link) {
            $copy->dealContacts()->create([
                'company_id' => $copy->company_id,
                'contact_id' => $link->contact_id,
                'role' => $link->role,
            ]);
        }

        foreach ($sourceProducts as $line) {
            $copy->products()->create([
                'company_id' => $copy->company_id,
                'product_id' => $line->product_id,
                'description' => $line->description,
                'quantity' => $line->quantity,
                'unit_price_cents' => $line->unit_price_cents,
                'discount_percent' => $line->discount_percent,
            ]);
        }

        return $copy;
    }

    /** Σ value × probability across open deals — integer money math only. */
    public function weightedPipelineValue(): Money
    {
        $company = app(CompanyContext::class)->current();
        $total = Money::ofMinor(0, $company->currency ?? 'EUR');

        Deal::query()
            ->where('status', 'open')
            ->get(['value_cents', 'probability', 'currency'])
            ->each(function (Deal $deal) use (&$total): void {
                $weighted = Money::ofMinor($deal->value_cents, $deal->currency)
                    ->multipliedBy((string) ((float) $deal->probability / 100), RoundingMode::HalfUp);
                $total = $total->plus($weighted);
            });

        return $total;
    }
}
