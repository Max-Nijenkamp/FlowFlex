<?php

declare(strict_types=1);

namespace App\Livewire\Crm;

use App\Contracts\Crm\DealServiceInterface;
use App\Data\Crm\CreateDealData;
use App\Exceptions\Crm\ClosedDealImmutableException;
use App\Models\Crm\Pipeline;
use App\Models\User;
use App\Services\Crm\PipelineService;
use App\Support\Services\CompanyContext;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Throwable;

/**
 * The kanban board (crm.pipeline/kanban-board): one pipeline at a time
 * with a switcher for the rest; HTML5 drag between columns →
 * DealService::moveToStage (closed-deal guard + probability reset +
 * DealStageChanged broadcast ride along). Remote moves patch in via the
 * Echo listener when Reverb creds exist.
 */
class PipelineBoard extends Component
{
    #[Url(as: 'pipeline')]
    public ?string $pipelineId = null;

    public ?string $ownerFilter = null;

    /** @var array<string, string> quick-add input per stage id */
    public array $quickAdd = [];

    public function selectPipeline(string $pipelineId): void
    {
        $this->pipelineId = $pipelineId;
        $this->quickAdd = [];
    }

    public function moveDeal(string $dealId, string $stageId): void
    {
        try {
            app(DealServiceInterface::class)->moveToStage($dealId, $stageId);
        } catch (ClosedDealImmutableException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        } catch (Throwable) {
            Notification::make()->danger()->title('Move failed — the board was refreshed.')->send();
        }
    }

    public function quickAddDeal(string $stageId): void
    {
        $name = trim($this->quickAdd[$stageId] ?? '');

        if ($name === '') {
            return;
        }

        $user = Auth::user();

        if (! ($user instanceof User && $user->can('crm.deals.create'))) {
            Notification::make()->danger()->title('You cannot create deals.')->send();

            return;
        }

        app(DealServiceInterface::class)->create(new CreateDealData(
            name: $name,
            stageId: $stageId,
        ));

        $this->quickAdd[$stageId] = '';
        Notification::make()->success()->title('Deal added')->send();
    }

    /** @return array<string, mixed> */
    protected function getListeners(): array
    {
        $companyId = app(CompanyContext::class)->currentId();

        return $companyId === null ? [] : [
            "echo-private:company.{$companyId},.crm.deal-stage-changed" => '$refresh',
        ];
    }

    public function render(): View
    {
        $pipeline = PipelineService::resolvePipeline($this->pipelineId);
        $this->pipelineId = $pipeline?->id;

        /** @var Collection<int, User> $owners */
        $owners = User::query()->get();

        return view('livewire.crm.pipeline-board', [
            'pipelines' => PipelineService::pipelines(),
            'pipeline' => $pipeline,
            'columns' => $pipeline instanceof Pipeline
                ? app(PipelineService::class)->board($pipeline, $this->ownerFilter)
                : collect(),
            'owners' => $owners,
        ]);
    }
}
