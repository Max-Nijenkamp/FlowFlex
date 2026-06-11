<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\Finance\Account;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Ledger';

    protected static ?string $modelLabel = 'GL account';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.ledger.view-any')
            && app(BillingServiceInterface::class)->hasModule('finance.ledger');
    }

    public static function canCreate(): bool
    {
        return false; // chart created on demand by LedgerService
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->orderBy('code'))
            ->columns([
                TextColumn::make('code')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')->badge(),
                IconColumn::make('is_active')->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AccountResource\Pages\ListAccounts::route('/'),
        ];
    }
}
