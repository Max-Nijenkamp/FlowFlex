<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources;

use App\Models\Crm\PipelineStage;
use App\Models\User;
use App\Services\BillingService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Board columns (crm.pipeline). Reorderable; a stage with deals cannot
 * be deleted — reassign the deals first.
 */
class PipelineStageResource extends Resource
{
    protected static ?string $model = PipelineStage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Pipeline stages';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('crm.pipeline.manage')
            && app(BillingService::class)->hasModule('crm.pipeline');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Stage')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(80),
                    TextInput::make('probability_default')
                        ->label('Default probability (%)')
                        ->numeric()->minValue(0)->maxValue(100)->required(),
                    Toggle::make('is_won')->label('Won stage'),
                    Toggle::make('is_lost')->label('Lost stage'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('probability_default')
                    ->label('Probability')
                    ->formatStateUsing(fn (PipelineStage $record): string => rtrim(rtrim((string) $record->probability_default, '0'), '.').'%'),
                IconColumn::make('is_won')->label('Won')->boolean(),
                IconColumn::make('is_lost')->label('Lost')->boolean(),
                TextColumn::make('deals_count')->label('Deals')->counts('deals'),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, PipelineStage $record): void {
                        if ($record->deals()->exists()) {
                            Notification::make()->danger()
                                ->title('This stage still holds deals — move them first.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->emptyStateHeading('No stages yet')
            ->emptyStateDescription('Activate the deals module to seed the default pipeline, or add stages here.');
    }

    public static function getPages(): array
    {
        return [
            'index' => PipelineStageResource\Pages\ListPipelineStages::route('/'),
        ];
    }
}
