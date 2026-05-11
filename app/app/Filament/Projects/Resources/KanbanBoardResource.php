<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Resources\KanbanBoardResource\Pages\CreateKanbanBoard;
use App\Filament\Projects\Resources\KanbanBoardResource\Pages\EditKanbanBoard;
use App\Filament\Projects\Resources\KanbanBoardResource\Pages\ListKanbanBoards;
use App\Models\Projects\KanbanBoard;
use App\Models\Projects\Project;
use App\Support\Services\CompanyContext;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KanbanBoardResource extends Resource
{
    protected static ?string $model = KanbanBoard::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-view-columns';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Planning';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kanban Boards';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        if (! auth()->check()) {
            return false;
        }
        $ctx = app(CompanyContext::class);
        if (! $ctx->hasCompany()) {
            return false;
        }

        return app(\App\Services\Core\BillingService::class)
            ->enforceModuleAccess($ctx->current(), 'projects.kanban');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Board Details')->columnSpanFull()->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
                Select::make('project_id')
                    ->label('Project')
                    ->options(fn () => Project::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Toggle::make('is_default')
                    ->label('Default board'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->placeholder('No project'),
                TextColumn::make('columns_count')
                    ->label('Columns')
                    ->counts('columns'),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListKanbanBoards::route('/'),
            'create' => CreateKanbanBoard::route('/create'),
            'edit'   => EditKanbanBoard::route('/{record}/edit'),
        ];
    }
}
