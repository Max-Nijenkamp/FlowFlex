<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\Finance\ExchangeRate;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|UnitEnum|null $navigationGroup = 'Configuration';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.currency.view')
            && app(BillingServiceInterface::class)->hasModule('finance.currency');
    }

    public static function canCreate(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.currency.manage');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Exchange rate')
                ->columns(2)
                ->components([
                    TextInput::make('from_currency')->required()->length(3)
                        ->helperText('Three-letter ISO code, e.g. EUR'),
                    TextInput::make('to_currency')->required()->length(3)
                        ->helperText('Three-letter ISO code, e.g. USD'),
                    TextInput::make('rate')->numeric()->step(0.00000001)->minValue(0)->required(),
                    DatePicker::make('effective_date')->required()->default(now()),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('from_currency'),
                TextColumn::make('to_currency'),
                TextColumn::make('rate'),
                TextColumn::make('effective_date')->date()->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => Auth::guard('web')->user()->can('finance.currency.manage')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ExchangeRateResource\Pages\ListExchangeRates::route('/'),
        ];
    }
}
