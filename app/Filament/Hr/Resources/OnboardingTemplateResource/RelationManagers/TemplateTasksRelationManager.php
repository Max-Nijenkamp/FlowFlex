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
    protected static string $relationship = 'templateTasks';

    protected static ?string $title = 'Tasks';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Details')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Select::make('task_type')
                        ->label('Task Type')
                        ->options(
                            collect(OnboardingTaskType::cases())
                                ->mapWithKeys(fn (OnboardingTaskType $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->required(),

                    TextInput::make('due_day_offset')
                        ->label('Due (days from start)')
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
                    ->label('#'),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('task_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?OnboardingTaskType $state) => $state?->label()),

                TextColumn::make('due_day_offset')
                    ->label('Due (days from start)')
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
