<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\Finance\Forecast;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ForecastResource extends Resource
{
    protected static ?string $model = Forecast::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static string|UnitEnum|null $navigationGroup = 'Planning';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.forecasting.view-any')
            && app(BillingServiceInterface::class)->hasModule('finance.forecasting');
    }

    public static function canCreate(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.forecasting.create');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Forecast')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(120),
                    Select::make('scenario')
                        ->options([
                            'base' => 'Base',
                            'optimistic' => 'Optimistic',
                            'pessimistic' => 'Pessimistic',
                        ])
                        ->default('base')
                        ->required(),
                    TextInput::make('fiscal_year')
                        ->numeric()->integer()->minValue(2000)->maxValue(2100)
                        ->default(now()->year)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('scenario')->badge(),
                TextColumn::make('fiscal_year'),
                TextColumn::make('lines_count')->counts('lines')->label('Lines'),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => Auth::guard('web')->user()->can('finance.forecasting.update')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ForecastResource\Pages\ListForecasts::route('/'),
        ];
    }
}
