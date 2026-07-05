<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources;

use App\Models\Crm\Deal;
use App\Models\Crm\Pipeline;
use App\Models\User;
use App\Services\BillingService;
use App\Services\Crm\PipelineService;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Pipelines (crm.pipeline, ADR custom-pipelines): as many as the sales
 * motion needs — per team, per person, per product. Each carries its
 * own stage set; the board switches between them.
 */
class PipelineResource extends Resource
{
    protected static ?string $model = Pipeline::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Pipelines';

    protected static ?string $slug = 'pipelines';

    protected static ?int $navigationSort = 0;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('crm.pipeline.manage')
            && app(BillingService::class)->hasModule('crm.pipeline');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                IconColumn::make('is_default')->label('Default')->boolean(),
                TextColumn::make('stages_count')->label('Stages')->counts('stages'),
                TextColumn::make('open_deals')
                    ->label('Open deals')
                    ->state(fn (Pipeline $record): string => (string) Deal::query()
                        ->where('status', 'open')
                        ->whereIn('stage_id', $record->stages()->pluck('id'))
                        ->count()),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->headerActions([
                CreateAction::make()
                    ->label('New pipeline')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(120)
                            ->placeholder('e.g. Partnerships, Anna’s pipeline'),
                        Toggle::make('seed_stages')
                            ->label('Start with the standard stages')
                            ->helperText('Lead → Qualified → Proposal → Won / Lost. Off = empty, add your own.')
                            ->default(true),
                        Toggle::make('is_default')
                            ->label('Make this the default pipeline'),
                    ])
                    ->using(fn (array $data): Pipeline => PipelineService::createPipeline(
                        app(CompanyContext::class)->current()->id,
                        $data['name'],
                        isDefault: (bool) ($data['is_default'] ?? false),
                        seedStages: (bool) ($data['seed_stages'] ?? true),
                    )),
            ])
            ->recordActions([
                Action::make('makeDefault')
                    ->label('Make default')
                    ->icon('heroicon-o-star')
                    ->visible(fn (Pipeline $record): bool => ! $record->is_default)
                    ->action(function (Pipeline $record): void {
                        Pipeline::query()->whereKeyNot($record->id)->update(['is_default' => false]);
                        $record->update(['is_default' => true]);
                        Notification::make()->success()->title("{$record->name} is now the default")->send();
                    }),
                EditAction::make()
                    ->schema([
                        TextInput::make('name')->required()->maxLength(120),
                    ]),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Pipeline $record): void {
                        $hasDeals = Deal::query()
                            ->whereIn('stage_id', $record->stages()->pluck('id'))
                            ->exists();

                        if ($hasDeals || $record->is_default) {
                            Notification::make()->danger()
                                ->title($record->is_default
                                    ? 'The default pipeline cannot be deleted — make another one default first.'
                                    : 'This pipeline still holds deals — move them first.')
                                ->send();
                            $action->cancel();
                        }
                    })
                    ->after(function (Pipeline $record): void {
                        $record->stages()->delete(); // empty stage set goes with it
                    }),
            ])
            ->emptyStateHeading('No pipelines yet')
            ->emptyStateDescription('Create one per team, person, or sales motion — each gets its own stages and board.');
    }

    public static function getPages(): array
    {
        return [
            'index' => PipelineResource\Pages\ListPipelines::route('/'),
        ];
    }
}
