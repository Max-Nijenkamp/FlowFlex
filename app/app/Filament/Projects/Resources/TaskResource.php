<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Resources\TaskResource\Pages\CreateTask;
use App\Filament\Projects\Resources\TaskResource\Pages\EditTask;
use App\Filament\Projects\Resources\TaskResource\Pages\ListTasks;
use App\Models\Projects\Project;
use App\Models\Projects\Task;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-check-circle';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'My Work';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tasks';
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
            ->enforceModuleAccess($ctx->current(), 'projects.tasks');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Details')->columnSpanFull()->schema([
                TextInput::make('title')
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
                Select::make('assignee_id')
                    ->label('Assignee')
                    ->options(fn () => User::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('email', 'id'))
                    ->searchable()
                    ->nullable(),
                Select::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->default('medium')
                    ->required(),
                Select::make('status')
                    ->options([
                        'todo'        => 'To Do',
                        'in_progress' => 'In Progress',
                        'in_review'   => 'In Review',
                        'done'        => 'Done',
                        'cancelled'   => 'Cancelled',
                    ])
                    ->default('todo')
                    ->required(),
                DatePicker::make('start_date')->nullable(),
                DatePicker::make('due_date')->nullable(),
                TextInput::make('estimate_hours')
                    ->numeric()
                    ->suffix('hrs')
                    ->nullable(),
                TextInput::make('story_points')
                    ->numeric()
                    ->nullable(),
                TagsInput::make('labels')->nullable(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable(),
                TextColumn::make('assignee.email')
                    ->label('Assignee')
                    ->placeholder('Unassigned'),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'done'        => 'success',
                        'in_progress' => 'warning',
                        'in_review'   => 'info',
                        'todo'        => 'gray',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('due_date')->date(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'todo'        => 'To Do',
                        'in_progress' => 'In Progress',
                        'in_review'   => 'In Review',
                        'done'        => 'Done',
                        'cancelled'   => 'Cancelled',
                    ]),
                SelectFilter::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                        'urgent' => 'Urgent',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ]);
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
