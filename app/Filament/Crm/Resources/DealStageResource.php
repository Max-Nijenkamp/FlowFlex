<?php

namespace App\Filament\Crm\Resources;

use App\Filament\Crm\Enums\NavigationGroup;
use App\Filament\Crm\Resources\DealStageResource\Pages\CreateDealStage;
use App\Filament\Crm\Resources\DealStageResource\Pages\EditDealStage;
use App\Filament\Crm\Resources\DealStageResource\Pages\ListDealStages;
use App\Models\Crm\DealStage;
use App\Models\Crm\Pipeline;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DealStageResource extends Resource
{
    protected static ?string $model = DealStage::class;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Sales->label();
    }

    public static function getModelLabel(): string
    {
        return __('crm.resources.deal_stages.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm.resources.deal_stages.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('crm.deal-stages.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('crm.deal-stages.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('crm.deal-stages.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('crm.deal-stages.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('crm.resources.deal_stages.sections.details'))
                ->schema([
                    Select::make('pipeline_id')
                        ->label(__('crm.resources.deal_stages.fields.pipeline'))
                        ->options(fn () => Pipeline::query()->pluck('name', 'id')->toArray())
                        ->required()
                        ->searchable(),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('sort_order')
                        ->label(__('crm.resources.deal_stages.fields.sort_order'))
                        ->numeric()
                        ->default(0),

                    TextInput::make('probability')
                        ->label(__('crm.resources.deal_stages.fields.probability'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->default(0)
                        ->suffix('%'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pipeline.name')
                    ->label(__('crm.resources.deal_stages.columns.pipeline'))
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('crm.resources.deal_stages.columns.order'))
                    ->sortable(),

                TextColumn::make('probability')
                    ->suffix('%')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->striped()
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('pipeline');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDealStages::route('/'),
            'create' => CreateDealStage::route('/create'),
            'edit'   => EditDealStage::route('/{record}/edit'),
        ];
    }
}
