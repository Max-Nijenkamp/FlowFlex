<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\Finance\Supplier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Payables';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.ap.manage-suppliers')
            && app(BillingServiceInterface::class)->hasModule('finance.ap');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->placeholder('—'),
                TextColumn::make('iban_last4')->label('IBAN')->formatStateUsing(fn (?string $state) => $state !== null ? '•••• '.$state : '—'),
                TextColumn::make('payment_terms_days')->label('Terms (days)'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => SupplierResource\Pages\ListSuppliers::route('/'),
        ];
    }
}
