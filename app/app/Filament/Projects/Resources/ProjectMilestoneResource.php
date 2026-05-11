<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Resources\ProjectMilestoneResource\Pages\CreateProjectMilestone;
use App\Filament\Projects\Resources\ProjectMilestoneResource\Pages\EditProjectMilestone;
use App\Filament\Projects\Resources\ProjectMilestoneResource\Pages\ListProjectMilestones;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectMilestone;
use App\Support\Services\CompanyContext;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectMilestoneResource extends Resource
{
    protected static ?string $model = ProjectMilestone::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-flag';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Planning';
    }

    public static function getNavigationLabel(): string
    {
        return 'Milestones';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
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
            ->enforceModuleAccess($ctx->current(), 'projects.milestones');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Milestone Details')->columnSpanFull()->schema([
                Select::make('project_id')
                    ->label('Project')
                    ->options(fn () => Project::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
                DatePicker::make('due_date')->required(),
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
                TextColumn::make('due_date')->date()->sortable(),
                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime()
                    ->placeholder('Not completed'),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProjectMilestones::route('/'),
            'create' => CreateProjectMilestone::route('/create'),
            'edit'   => EditProjectMilestone::route('/{record}/edit'),
        ];
    }
}
