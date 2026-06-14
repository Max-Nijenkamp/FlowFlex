<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Pipeline;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Custom pipelines (Pipedrive pattern) — multiple per company, each with
 * its own stages, managed by the team itself.
 */
class PipelineResource extends Resource
{
    protected static ?string $model = Pipeline::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    protected static ?string $navigationLabel = 'Pipelines';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.pipeline.view')
            && app(BillingServiceInterface::class)->hasModule('crm.pipeline');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pipeline')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(80),
                    Toggle::make('is_default')->label('Default pipeline')
                        ->helperText('Opens first on the pipeline board.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('stages')->orderBy('order'))
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('stages_count')->label('Stages'),
                IconColumn::make('is_default')->label('Default')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    // Refuse when any stage still carries deals.
                    ->visible(fn (Pipeline $record): bool => ! $record->stages()
                        ->whereHas('deals')->exists()),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PipelineResource\RelationManagers\StagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => PipelineResource\Pages\ListPipelines::route('/'),
            'edit' => PipelineResource\Pages\EditPipeline::route('/{record}/edit'),
        ];
    }
}
