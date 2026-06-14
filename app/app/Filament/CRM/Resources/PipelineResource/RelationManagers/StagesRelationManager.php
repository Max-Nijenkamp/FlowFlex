<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\PipelineResource\RelationManagers;

use App\Models\CRM\PipelineStage;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StagesRelationManager extends RelationManager
{
    protected static string $relationship = 'stages';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(60),
            TextInput::make('order')->numeric()->default(0)
                ->helperText('Left-to-right position on the board.'),
            TextInput::make('probability_default')->numeric()->default(20)
                ->suffix('%')->minValue(0)->maxValue(100)
                ->helperText('Default win chance for deals entering this stage.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->orderBy('order'))
            ->columns([
                TextColumn::make('order')->sortable(),
                TextColumn::make('name'),
                TextColumn::make('probability_default')->suffix('%'),
                TextColumn::make('deals_count')->label('Open deals')
                    ->state(fn (PipelineStage $r): int => $r->deals()->where('status', 'open')->count()),
            ])
            ->headerActions([
                CreateAction::make()->label('Add stage'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, PipelineStage $record): void {
                        if ($record->deals()->exists()) {
                            Notification::make()->danger()
                                ->title('Stage still has deals — move them first.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ]);
    }
}
