<?php

namespace App\Filament\Hr\Resources\OnboardingTemplateResource\RelationManagers;

use App\Enums\Hr\OnboardingTaskType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TemplateTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('hr.resources.onboarding_templates.relation_managers.tasks.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('hr.resources.onboarding_templates.relation_managers.tasks.sections.details'))
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Select::make('task_type')
                        ->label(__('hr.resources.onboarding_templates.relation_managers.tasks.fields.task_type'))
                        ->options(
                            collect(OnboardingTaskType::cases())
                                ->mapWithKeys(fn (OnboardingTaskType $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->required(),

                    TextInput::make('due_day_offset')
                        ->label(__('hr.resources.onboarding_templates.relation_managers.tasks.fields.due_day_offset'))
                        ->numeric()
                        ->required()
                        ->default(0),

                    TextInput::make('order')
                        ->numeric()
                        ->default(0),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->sortable()
                    ->label(__('hr.resources.onboarding_templates.relation_managers.tasks.columns.order')),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('task_type')
                    ->label(__('hr.resources.onboarding_templates.relation_managers.tasks.columns.type'))
                    ->badge()
                    ->formatStateUsing(fn (?OnboardingTaskType $state) => $state?->label()),

                TextColumn::make('due_day_offset')
                    ->label(__('hr.resources.onboarding_templates.relation_managers.tasks.columns.due_day_offset'))
                    ->numeric(),
            ])
            ->defaultSort('order')
            ->striped()
            ->headerActions([
                CreateAction::make(),
            ])
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
}
