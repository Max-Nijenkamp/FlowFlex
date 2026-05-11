<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Resources\SprintResource\Pages\CreateSprint;
use App\Filament\Projects\Resources\SprintResource\Pages\EditSprint;
use App\Filament\Projects\Resources\SprintResource\Pages\ListSprints;
use App\Filament\Projects\Resources\SprintResource\Pages\ViewSprint;
use App\Models\Projects\Project;
use App\Models\Projects\Sprint;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SprintResource extends Resource
{
    protected static ?string $model = Sprint::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-rocket-launch';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Planning';
    }

    public static function getNavigationLabel(): string
    {
        return 'Sprints';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
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
            ->enforceModuleAccess($ctx->current(), 'projects.sprints');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Sprint Details')->columnSpanFull()->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Sprint 1'),
                Textarea::make('goal')
                    ->nullable()
                    ->columnSpanFull(),
                Select::make('project_id')
                    ->label('Project')
                    ->options(fn () => Project::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->options([
                        'planning'  => 'Planning',
                        'active'    => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('planning')
                    ->required(),
                DatePicker::make('start_date')->nullable(),
                DatePicker::make('end_date')->nullable(),
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
                    ->label('Project'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'planning'  => 'info',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('start_date')->date(),
                TextColumn::make('end_date')->date()->label('End Date'),
                TextColumn::make('velocity')
                    ->label('Velocity (pts)')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'planning'  => 'Planning',
                        'active'    => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()
                    ->label('View Board')
                    ->icon('heroicon-o-view-columns'),
                EditAction::make(),
                Action::make('start_sprint')
                    ->label('Start Sprint')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (Sprint $record) => $record->status === 'planning')
                    ->requiresConfirmation()
                    ->action(function (Sprint $record): void {
                        $record->update(['status' => 'active', 'start_date' => $record->start_date ?? now()->toDateString()]);
                        Notification::make()->title('Sprint started')->success()->send();
                    }),
                Action::make('complete_sprint')
                    ->label('Complete Sprint')
                    ->icon('heroicon-o-check')
                    ->color('gray')
                    ->visible(fn (Sprint $record) => $record->status === 'active')
                    ->requiresConfirmation()
                    ->action(function (Sprint $record): void {
                        $record->update(['status' => 'completed', 'end_date' => $record->end_date ?? now()->toDateString()]);
                        Notification::make()->title('Sprint completed')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSprints::route('/'),
            'create' => CreateSprint::route('/create'),
            'view'   => ViewSprint::route('/{record}'),
            'edit'   => EditSprint::route('/{record}/edit'),
        ];
    }
}
