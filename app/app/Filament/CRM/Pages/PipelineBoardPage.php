<?php

declare(strict_types=1);

namespace App\Filament\CRM\Pages;

use App\Contracts\BillingServiceInterface;
use App\Contracts\CRM\DealServiceInterface;
use App\Models\CRM\Deal;
use App\Models\CRM\PipelineStage;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * ui-strategy row #3 — Kanban custom page. v1: stage-move buttons (Alpine
 * drag-drop + Reverb broadcast land with the realtime pass).
 */
class PipelineBoardPage extends Page
{
    protected string $view = 'filament.crm.pages.pipeline-board';

    /** Deferred first paint — blade shows <x-skeleton.board> until wire:init fires. */
    public bool $readyToLoad = false;

    public function loadBoard(): void
    {
        $this->readyToLoad = true;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    protected static ?string $title = 'Pipeline';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.pipeline.view')
            && app(BillingServiceInterface::class)->hasModule('crm.pipeline');
    }

    /** @return Collection<int, PipelineStage> */
    public function getStages()
    {
        return PipelineStage::query()->orderBy('order')->get();
    }

    /** @return Collection<int, Deal> */
    public function getDealsFor(string $stageId)
    {
        return Deal::query()
            ->where('stage_id', $stageId)
            ->where('status', 'open')
            ->orderByDesc('value_cents')
            ->get();
    }

    public function moveDeal(string $dealId, string $stageId): void
    {
        app(DealServiceInterface::class)->moveToStage($dealId, $stageId);
        Notification::make()->success()->title('Deal moved')->send();
    }
}
