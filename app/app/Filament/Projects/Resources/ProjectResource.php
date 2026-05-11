<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Projects\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Projects\Resources\ProjectResource\Pages\ListProjects;
use App\Models\Projects\Project;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-folder';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Projects';
    }

    public static function getNavigationLabel(): string
    {
        return 'Projects';
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
            Section::make('Project Details')->columnSpanFull()->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
                Select::make('owner_id')
                    ->label('Owner')
                    ->options(fn () => User::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('email', 'id'))
                    ->searchable()
                    ->required(),
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
                        'planning'  => 'Planning',
                        'active'    => 'Active',
                        'on_hold'   => 'On Hold',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('planning')
                    ->required(),
                DatePicker::make('start_date')->nullable(),
                DatePicker::make('due_date')->nullable(),
                TextInput::make('budget')
                    ->numeric()
                    ->prefix('$')
                    ->nullable(),
                ColorPicker::make('color')->nullable(),
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
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'planning'  => 'info',
                        'on_hold'   => 'warning',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    }),
                TextColumn::make('owner.email')
                    ->label('Owner')
                    ->searchable(),
                TextColumn::make('start_date')->date(),
                TextColumn::make('due_date')->date(),
                TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->counts('tasks'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'planning'  => 'Planning',
                        'active'    => 'Active',
                        'on_hold'   => 'On Hold',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
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
            'index'  => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit'   => EditProject::route('/{record}/edit'),
        ];
    }
}
