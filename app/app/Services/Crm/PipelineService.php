<?php

declare(strict_types=1);

namespace App\Services\Crm;

use App\Models\Company;
use App\Models\Crm\Deal;
use App\Models\Crm\Pipeline;
use App\Models\Crm\PipelineStage;
use Brick\Money\Money;
use Illuminate\Support\Collection;

/**
 * Board composition + stage reference data (crm.pipeline). Board reads
 * crm_deals in ONE grouped query; stage moves go through DealService.
 */
class PipelineService
{
    /**
     * Default stage set, seeded on module activation (spec *(assumed)*):
     * Lead → Qualified → Proposal → Won / Lost.
     */
    public static function ensureDefaultStages(Company $company): Pipeline
    {
        /** @var Pipeline|null $pipeline */
        $pipeline = Pipeline::query()
            ->where('company_id', $company->id)
            ->where('is_default', true)
            ->first();

        if ($pipeline instanceof Pipeline) {
            return $pipeline;
        }

        $pipeline = Pipeline::query()->create([
            'company_id' => $company->id,
            'name' => 'Sales pipeline',
            'is_default' => true,
        ]);

        $defaults = [
            ['name' => 'Lead', 'order' => 1, 'probability_default' => 10, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Qualified', 'order' => 2, 'probability_default' => 30, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Proposal', 'order' => 3, 'probability_default' => 60, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Won', 'order' => 4, 'probability_default' => 100, 'is_won' => true, 'is_lost' => false],
            ['name' => 'Lost', 'order' => 5, 'probability_default' => 0, 'is_won' => false, 'is_lost' => true],
        ];

        foreach ($defaults as $stage) {
            PipelineStage::query()->create($stage + [
                'company_id' => $company->id,
                'pipeline_id' => $pipeline->id,
            ]);
        }

        return $pipeline;
    }

    /**
     * Stages with their open-deal cards and per-stage totals — one deal
     * query for the whole board.
     *
     * @return Collection<int, array{stage: PipelineStage, deals: Collection<int, Deal>, total: Money, count: int}>
     */
    public function board(?string $ownerId = null): Collection
    {
        $stages = PipelineStage::query()->get()->sortBy('order')->values();

        $deals = Deal::query()
            ->where('status', 'open')
            ->when($ownerId, fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['account:id,name', 'owner:id,first_name,last_name'])
            ->orderByDesc('value_cents')
            ->get()
            ->groupBy('stage_id');

        return $stages->map(function (PipelineStage $stage) use ($deals): array {
            /** @var Collection<int, Deal> $stageDeals */
            $stageDeals = $deals->get($stage->id, collect());

            $total = Money::ofMinor(0, 'EUR');
            foreach ($stageDeals as $deal) {
                $total = $total->plus(Money::ofMinor($deal->value_cents, $deal->currency));
            }

            return [
                'stage' => $stage,
                'deals' => $stageDeals,
                'total' => $total,
                'count' => $stageDeals->count(),
            ];
        });
    }
}
