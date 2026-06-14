<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\Finance\TaxRate;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class TaxRateResource extends Resource
{
    protected static ?string $model = TaxRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|UnitEnum|null $navigationGroup = 'Configuration';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.tax.manage-rates')
            && app(BillingServiceInterface::class)->hasModule('finance.tax');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tax rate')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(120),
                    TextInput::make('rate_basis_points')->label('Rate (basis points)')
                        ->numeric()->integer()->minValue(0)->required()
                        ->helperText('2100 = 21%'),
                    Select::make('type')
                        ->options([
                            'vat' => 'VAT',
                            'sales' => 'Sales tax',
                            'withholding' => 'Withholding',
                        ])
                        ->default('vat')
                        ->required(),
                    TextInput::make('jurisdiction')->required()->length(2)
                        ->helperText('Two-letter country code, e.g. NL'),
                    Toggle::make('is_reverse_charge')->label('Reverse charge'),
                    Toggle::make('is_active')->default(true),
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
                TextColumn::make('rate_basis_points')->label('Rate')->formatStateUsing(fn (int $state) => number_format($state / 100, 2).'%'),
                TextColumn::make('jurisdiction')->badge(),
                TextColumn::make('is_reverse_charge')->label('Reverse charge')->badge()->formatStateUsing(fn (bool $state) => $state ? 'yes' : 'no'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => TaxRateResource\Pages\ListTaxRates::route('/'),
        ];
    }
}
