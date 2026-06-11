<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\Finance\BankAccount;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|UnitEnum|null $navigationGroup = 'Ledger';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.bank.view-any')
            && app(BillingServiceInterface::class)->hasModule('finance.bank');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('bank_name')->required(),
            TextInput::make('iban')
                ->visible(fn () => Auth::guard('web')->user()->can('finance.bank.view-sensitive'))
                ->dehydrateStateUsing(function (?string $state) {
                    return $state;
                })
                ->afterStateUpdated(fn ($state, $set) => $set('iban_last4', $state !== null ? substr($state, -4) : null)),
            TextInput::make('currency')->default('EUR')->maxLength(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('bank_name'),
                TextColumn::make('iban_last4')->label('IBAN')->formatStateUsing(fn (?string $state) => $state ? "•••• {$state}" : '—'),
                TextColumn::make('current_balance_cents')->label('Balance')
                    ->formatStateUsing(fn (int $state, BankAccount $r) => number_format($state / 100, 2).' '.$r->currency),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => BankAccountResource\Pages\ListBankAccounts::route('/'),
            'create' => BankAccountResource\Pages\CreateBankAccount::route('/create'),
            'edit' => BankAccountResource\Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
