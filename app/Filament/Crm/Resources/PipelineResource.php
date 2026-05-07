<?php

namespace App\Filament\Crm\Resources;

use App\Filament\Crm\Enums\NavigationGroup;
use App\Filament\Crm\Resources\PipelineResource\Pages\CreatePipeline;
use App\Filament\Crm\Resources\PipelineResource\Pages\EditPipeline;
use App\Filament\Crm\Resources\PipelineResource\Pages\ListPipelines;
use App\Models\Crm\Pipeline;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PipelineResource extends Resource
{
    protected static ?string $model = Pipeline::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Sales->label();
    }

    public static function getModelLabel(): string
    {
        return __('crm.resources.pipelines.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm.resources.pipelines.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('crm.pipelines.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('crm.pipelines.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('crm.pipelines.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('crm.pipelines.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('crm.resources.pipelines.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Toggle::make('is_default')
                        ->label(__('crm.resources.pipelines.fields.is_default'))
                        ->default(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('stages_count')
                    ->label(__('crm.resources.pipelines.columns.stages'))
                    ->counts('stages'),

                TextColumn::make('deals_count')
                    ->label(__('crm.resources.pipelines.columns.deals'))
                    ->counts('deals'),

                IconColumn::make('is_default')
                    ->label(__('crm.resources.pipelines.columns.default'))
                    ->boolean(),
            ])
            ->defaultSort('name')
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

    public static function getPages(): array
    {
        return [
            'index'  => ListPipelines::route('/'),
            'create' => CreatePipeline::route('/create'),
            'edit'   => EditPipeline::route('/{record}/edit'),
        ];
    }
}
