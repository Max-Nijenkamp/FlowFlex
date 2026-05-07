<?php

namespace App\Filament\Projects\Resources;

use App\Enums\Projects\TaskPriority;
use App\Enums\Projects\TaskStatus;
use App\Filament\Projects\Enums\NavigationGroup;
use App\Filament\Projects\Resources\TaskResource\Pages\CreateTask;
use App\Filament\Projects\Resources\TaskResource\Pages\EditTask;
use App\Filament\Projects\Resources\TaskResource\Pages\ListTasks;
use App\Models\Projects\Task;
use App\Models\Projects\TaskLabel;
use App\Models\Tenant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-check-circle';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Tasks;

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('projects.tasks.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('projects.tasks.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('projects.tasks.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('projects.tasks.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Details')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->nullable()
                        ->rows(4),

                    Select::make('priority')
                        ->options(
                            collect(TaskPriority::cases())
                                ->mapWithKeys(fn (TaskPriority $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(TaskPriority::Medium->value)
                        ->required(),

                    Select::make('status')
                        ->options(
                            collect(TaskStatus::cases())
                                ->mapWithKeys(fn (TaskStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(TaskStatus::Todo->value)
                        ->required(),
                ]),

            Section::make('Assignment')
                ->schema([
                    Select::make('assignee_tenant_id')
                        ->label('Assignee')
                        ->options(
                            fn () => Tenant::query()
                                ->where('company_id', auth()->user()?->company_id)
                                ->get()
                                ->mapWithKeys(fn (Tenant $tenant) => [$tenant->id => $tenant->fullName() ?: $tenant->email])
                                ->toArray()
                        )
                        ->nullable()
                        ->searchable(),

                    DatePicker::make('due_date')
                        ->nullable()
                        ->native(false),

                    DatePicker::make('start_date')
                        ->nullable()
                        ->native(false),

                    TextInput::make('estimated_hours')
                        ->label('Estimated Hours')
                        ->numeric()
                        ->nullable()
                        ->minValue(0),
                ])
                ->columns(2),

            Section::make('Labels')
                ->schema([
                    Select::make('labels')
                        ->relationship('labels', 'name')
                        ->multiple()
                        ->preload()
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (TaskStatus $state) => $state->label())
                    ->color(fn (TaskStatus $state) => $state->color())
                    ->sortable(),

                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (TaskPriority $state) => $state->label())
                    ->color(fn (TaskPriority $state) => $state->color())
                    ->sortable(),

                TextColumn::make('assignee.email')
                    ->label('Assignee')
                    ->placeholder('Unassigned')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (?string $state) => $state && now()->isAfter($state) ? 'danger' : null),

                TextColumn::make('estimated_hours')
                    ->label('Est. Hours')
                    ->numeric(2)
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(TaskStatus::cases())
                            ->mapWithKeys(fn (TaskStatus $case) => [$case->value => $case->label()])
                            ->toArray()
                    ),

                SelectFilter::make('priority')
                    ->options(
                        collect(TaskPriority::cases())
                            ->mapWithKeys(fn (TaskPriority $case) => [$case->value => $case->label()])
                            ->toArray()
                    ),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['assignee', 'labels']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'edit'   => EditTask::route('/{record}/edit'),
        ];
    }
}
