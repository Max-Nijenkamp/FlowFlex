<?php

declare(strict_types=1);

namespace App\Livewire\Crm;

use App\Contracts\Crm\DealServiceInterface;
use App\Data\Crm\CreateDealData;
use App\Exceptions\Crm\ClosedDealImmutableException;
use App\Models\Crm\PipelineStage;
use App\Models\User;
use App\Services\Crm\PipelineService;
use App\Support\Services\CompanyContext;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Throwable;

/**
 * The kanban board (crm.pipeline/kanban-board): HTML5 drag between
 * columns → DealService::moveToStage (closed-deal guard + probability
 * reset + DealStageChanged broadcast ride along). Remote moves patch in
 * via the Echo listener when Reverb creds exist; without them the board
 * simply doesn't live-sync.
 */
class PipelineBoard extends Component
{
    public ?string $ownerFilter = null;

    /** @var array<string, string> quick-add input per stage id */
    public array $quickAdd = [];

    public function moveDeal(string $dealId, string $stageId): void
    {
        try {
            app(DealServiceInterface::class)->moveToStage($dealId, $stageId);
        } catch (ClosedDealImmutableException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        } catch (Throwable $e) {
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
        // First visit before the activation listener ran: self-heal the stage set.
        if (PipelineStage::query()->count() === 0) {
            PipelineService::ensureDefaultStages(app(CompanyContext::class)->current());
        }

        /** @var Collection<int, User> $owners */
        $owners = User::query()->get();

        return view('livewire.crm.pipeline-board', [
            'columns' => app(PipelineService::class)->board($this->ownerFilter),
            'owners' => $owners,
        ]);
    }
}
