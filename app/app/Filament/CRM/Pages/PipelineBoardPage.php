<?php

declare(strict_types=1);

namespace App\Filament\CRM\Pages;

use App\Contracts\BillingServiceInterface;
use App\Contracts\CRM\DealServiceInterface;
use App\Models\CRM\Account;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Models\CRM\Pipeline;
use App\Models\CRM\PipelineStage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * ui-strategy row #3 — Kanban custom page with multiple pipelines
 * (Pipedrive pattern): switcher + per-pipeline custom stages.
 */
class PipelineBoardPage extends Page
{
    protected string $view = 'filament.crm.pages.pipeline-board';

    /** Deferred first paint — blade shows <x-skeleton.board> until wire:init fires. */
    public bool $readyToLoad = false;

    /** Active pipeline (switcher). */
    public ?string $pipelineId = null;

    public function loadBoard(): void
    {
        $this->readyToLoad = true;
    }

    public function mount(): void
    {
        $this->pipelineId = Pipeline::query()
            ->orderByDesc('is_default')
            ->orderBy('order')
            ->value('id');
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

    /** @return Collection<int, Pipeline> */
    public function getPipelines()
    {
        return Pipeline::query()->orderByDesc('is_default')->orderBy('order')->get();
    }

    public function switchPipeline(string $pipelineId): void
    {
        $this->pipelineId = $pipelineId;
    }

    /** @return Collection<int, PipelineStage> */
    public function getStages()
    {
        return PipelineStage::query()
            ->when($this->pipelineId !== null, fn ($q) => $q->where('pipeline_id', $this->pipelineId))
            ->orderBy('order')
            ->get();
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

    public function getPipelineValueCents(): int
    {
        $stageIds = $this->getStages()->pluck('id');

        return (int) Deal::query()
            ->whereIn('stage_id', $stageIds)
            ->where('status', 'open')
            ->sum('value_cents');
    }

    public function moveDeal(string $dealId, string $stageId): void
    {
        app(DealServiceInterface::class)->moveToStage($dealId, $stageId);
        Notification::make()->success()->title('Deal moved')->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('newDeal')
                ->label('New deal')
                ->icon(Heroicon::OutlinedPlus)
                ->visible(fn (): bool => Auth::guard('web')->user()->can('crm.deals.create'))
                ->schema([
                    TextInput::make('name')->required()->maxLength(120),
                    Select::make('stage_id')->label('Stage')
                        ->options(fn (): array => $this->getStages()->pluck('name', 'id')->all())
                        ->required(),
                    TextInput::make('value_cents')->label('Value (€)')->numeric()
                        ->helperText('Whole euros — stored exact.')
                        ->required(),
                    Select::make('contact_id')->label('Contact')
                        ->options(fn () => Contact::query()
                            ->orderBy('last_name')->limit(100)
                            ->get()
                            ->mapWithKeys(fn ($c) => [$c->id => trim("{$c->first_name} {$c->last_name}")]))
                        ->searchable(),
                    Select::make('account_id')->label('Organisation')
                        ->options(fn () => Account::query()
                            ->orderBy('name')->limit(100)->pluck('name', 'id'))
                        ->searchable(),
                    DatePicker::make('expected_close_date'),
                ])
                ->action(function (array $data): void {
                    $stage = PipelineStage::query()->findOrFail($data['stage_id']);

                    Deal::create([
                        'name' => $data['name'],
                        'stage_id' => $stage->id,
                        'value_cents' => ((int) $data['value_cents']) * 100,
                        'currency' => 'EUR',
                        'probability' => $stage->probability_default,
                        'status' => 'open',
                        'owner_id' => Auth::guard('web')->id(),
                        'contact_id' => $data['contact_id'] ?? null,
                        'account_id' => $data['account_id'] ?? null,
                        'expected_close_date' => $data['expected_close_date'] ?? null,
                        'stage_entered_at' => now(),
                    ]);

                    Notification::make()->success()->title('Deal created')->send();
                }),
        ];
    }
}
