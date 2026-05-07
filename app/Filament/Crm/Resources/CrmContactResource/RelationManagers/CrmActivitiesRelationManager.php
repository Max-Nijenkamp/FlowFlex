<?php

namespace App\Filament\Crm\Resources\CrmContactResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

class CrmActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->options([
                    'note'          => 'Note',
                    'call'          => 'Call',
                    'email'         => 'Email',
                    'meeting'       => 'Meeting',
                    'deal_update'   => 'Deal Update',
                    'ticket_update' => 'Ticket Update',
                ])
                ->required(),

            Textarea::make('description')
                ->required()
                ->rows(3),

            DateTimePicker::make('occurred_at')
                ->label('Occurred At')
                ->required()
                ->native(false)
                ->default(now()),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('description')
                    ->limit(80),

                TextColumn::make('occurred_at')
                    ->label('Occurred')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
