<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources;

use App\Models\Crm\Account;
use App\Models\User;
use App\Services\BillingService;
use Brick\Money\Money;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Company/organisation records (crm.contacts). LTV column is fed by the
 * finance InvoicePaid listener — read-only here.
 */
class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Contacts';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('crm.contacts.view-any')
            && app(BillingService::class)->hasModule('crm.contacts');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('crm.accounts.manage');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(160),
                    TextInput::make('industry')->maxLength(120),
                    TextInput::make('employee_count')->numeric()->minValue(1),
                    TextInput::make('website')->url()->maxLength(255),
                    TextInput::make('phone')->tel(),
                    Select::make('owner_id')
                        ->label('Owner')
                        ->options(fn (): array => User::query()->get()->mapWithKeys(
                            fn (User $user): array => [$user->id => $user->full_name],
                        )->all())
                        ->default(fn () => Auth::id())
                        ->required(),
                    KeyValue::make('custom_fields')
                        ->label('Custom fields')
                        ->keyLabel('Field')
                        ->valueLabel('Value')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('industry')->placeholder('—'),
                TextColumn::make('contacts_count')->label('Contacts')->counts('contacts'),
                TextColumn::make('deals_count')->label('Deals')->counts('deals'),
                TextColumn::make('lifetime_value_cents')
                    ->label('Lifetime value')
                    ->formatStateUsing(fn (Account $record): string => Money::ofMinor($record->lifetime_value_cents, 'EUR')->formatToLocale('nl_NL')),
                TextColumn::make('owner.full_name')->label('Owner'),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make()
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('crm.accounts.manage');
                    }),
                DeleteAction::make()
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('crm.accounts.manage');
                    }),
            ])
            ->emptyStateHeading('No accounts yet')
            ->emptyStateDescription('Accounts group your contacts and deals per organisation.');
    }

    public static function getPages(): array
    {
        return [
            'index' => AccountResource\Pages\ListAccounts::route('/'),
            'create' => AccountResource\Pages\CreateAccount::route('/create'),
            'edit' => AccountResource\Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
