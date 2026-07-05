<?php

declare(strict_types=1);

namespace App\Services\Crm;

use App\Models\Company;
use App\Models\Crm\Deal;
use App\Models\Crm\Pipeline;
use App\Models\Crm\PipelineStage;
use App\Support\Services\CompanyContext;
use Brick\Money\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Board composition + pipeline/stage reference data (crm.pipeline, ADR
 * custom-pipelines): a company runs any number of pipelines (per team,
 * per person, per motion), each with its own stage set. The board shows
 * one pipeline at a time; deals live in stages, stages in pipelines.
 */
class PipelineService
{
    public const DEFAULT_STAGES = [
        ['name' => 'Lead', 'order' => 1, 'probability_default' => 10, 'is_won' => false, 'is_lost' => false],
        ['name' => 'Qualified', 'order' => 2, 'probability_default' => 30, 'is_won' => false, 'is_lost' => false],
        ['name' => 'Proposal', 'order' => 3, 'probability_default' => 60, 'is_won' => false, 'is_lost' => false],
        ['name' => 'Won', 'order' => 4, 'probability_default' => 100, 'is_won' => true, 'is_lost' => false],
        ['name' => 'Lost', 'order' => 5, 'probability_default' => 0, 'is_won' => false, 'is_lost' => true],
    ];

    /** Activation/self-heal: make sure at least the default pipeline exists. */
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

        return self::createPipeline($company->id, 'Sales pipeline', isDefault: true, seedStages: true);
    }

    /** New pipeline, optionally pre-seeded with the standard stage set. */
    public static function createPipeline(string $companyId, string $name, bool $isDefault = false, bool $seedStages = true): Pipeline
    {
        return DB::transaction(function () use ($companyId, $name, $isDefault, $seedStages): Pipeline {
            if ($isDefault) {
                // exactly one default per company
                Pipeline::query()->where('company_id', $companyId)->update(['is_default' => false]);
            }

            /** @var Pipeline $pipeline */
            $pipeline = Pipeline::query()->create([
                'company_id' => $companyId,
                'name' => $name,
                'is_default' => $isDefault,
                'order' => (int) Pipeline::query()->where('company_id', $companyId)->max('order') + 1,
            ]);

            if ($seedStages) {
                foreach (self::DEFAULT_STAGES as $stage) {
                    PipelineStage::query()->create($stage + [
                        'company_id' => $companyId,
                        'pipeline_id' => $pipeline->id,
                    ]);
                }
            }

            return $pipeline;
        });
    }

    /** Switcher order: default first, then manual order. */
    public static function pipelines(): Collection
    {
        return Pipeline::query()->get()
            ->sortBy([['is_default', 'desc'], ['order', 'asc'], ['name', 'asc']])
            ->values();
    }

    public static function resolvePipeline(?string $pipelineId): ?Pipeline
    {
        if ($pipelineId !== null) {
            $picked = Pipeline::query()->find($pipelineId);

            if ($picked instanceof Pipeline) {
                return $picked;
            }
        }

        return self::pipelines()->first()
            ?? self::ensureDefaultStages(app(CompanyContext::class)->current());
    }

    /**
     * One pipeline's stages with their open-deal cards and per-stage
     * totals — one deal query for the whole board.
     *
     * @return Collection<int, array{stage: PipelineStage, deals: Collection<int, Deal>, total: Money, count: int}>
     */
    public function board(Pipeline $pipeline, ?string $ownerId = null): Collection
    {
        $stages = PipelineStage::query()
            ->where('pipeline_id', $pipeline->id)
            ->get()
            ->sortBy('order')
            ->values();

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
