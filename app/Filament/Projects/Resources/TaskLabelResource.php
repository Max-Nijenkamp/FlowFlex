<?php

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Enums\NavigationGroup;
use App\Filament\Projects\Resources\TaskLabelResource\Pages\CreateTaskLabel;
use App\Filament\Projects\Resources\TaskLabelResource\Pages\EditTaskLabel;
use App\Filament\Projects\Resources\TaskLabelResource\Pages\ListTaskLabels;
use App\Models\Projects\TaskLabel;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaskLabelResource extends Resource
{
    protected static ?string $model = TaskLabel::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Tasks->label();
    }

    public static function getModelLabel(): string
    {
        return __('projects.resources.task_labels.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('projects.resources.task_labels.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('projects.task-labels.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('projects.task-labels.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('projects.task-labels.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('projects.task-labels.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('projects.resources.task_labels.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('color')
                        ->label(__('projects.resources.task_labels.fields.color'))
                        ->nullable()
                        ->placeholder('#3B82F6')
                        ->maxLength(7),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                TextColumn::make('color')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ?? 'No color')
                    ->color(fn (?string $state) => $state ?? 'gray'),

                TextColumn::make('tasks_count')
                    ->label(__('projects.resources.task_labels.columns.tasks_count'))
                    ->counts('tasks')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->striped()
            ->actions([
                EditAction::make(),
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
            'index'  => ListTaskLabels::route('/'),
            'create' => CreateTaskLabel::route('/create'),
            'edit'   => EditTaskLabel::route('/{record}/edit'),
        ];
    }
}
