<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Models\Finance\Account;
use App\Models\Finance\BankAccount;
use App\Models\User;
use App\Services\BillingService;
use App\Services\Finance\BankService;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Bank accounts (finance.bank): CSV statement import with per-row error
 * report + dedupe, and a bank-vs-ledger balance check. IBAN and account
 * number are encrypted at rest; only iban_last4 shows in lists.
 */
class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static string|\UnitEnum|null $navigationGroup = 'Banking';

    protected static ?string $navigationLabel = 'Bank accounts';

    protected static ?string $slug = 'bank';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.bank.view')
            && app(BillingService::class)->hasModule('finance.bank');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('finance.bank.manage');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bank account')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(120),
                    TextInput::make('bank_name')->required()->maxLength(120),
                    TextInput::make('iban')
                        ->label('IBAN')
                        ->maxLength(42)
                        ->helperText('Stored encrypted — lists only ever show the last four characters.'),
                    TextInput::make('account_number')->label('Account number')->maxLength(42),
                    Select::make('gl_account_id')
                        ->label('Ledger account')
                        ->options(fn (): array => Account::query()
                            ->where('type', 'asset')
                            ->get()
                            ->sortBy('code')
                            ->mapWithKeys(fn (Account $account): array => [$account->id => "{$account->code} · {$account->name}"])
                            ->all())
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('bank_name'),
                TextColumn::make('iban_last4')
                    ->label('IBAN')
                    ->formatStateUsing(fn (?string $state): string => $state === null ? '—' : "•••• {$state}"),
                TextColumn::make('current_balance_cents')
                    ->label('Balance')
                    ->formatStateUsing(fn (BankAccount $record): string => Money::ofMinor($record->current_balance_cents, $record->currency)->formatToLocale('nl_NL')),
                TextColumn::make('unreconciled')
                    ->label('Unreconciled')
                    ->state(fn (BankAccount $record): string => (string) $record->transactions()->whereNull('reconciled_at')->count()),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('import')
                        ->label('Import statement')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->visible(function (): bool {
                            $user = Auth::user();

                            return $user instanceof User && $user->can('finance.bank.manage');
                        })
                        ->modalDescription('CSV with three columns: date, description, amount (signed decimal). A header row is skipped; re-importing the same rows is a no-op.')
                        ->schema([
                            FileUpload::make('statement')
                                ->acceptedFileTypes(['text/csv', 'text/plain'])
                                ->maxSize(10240)
                                ->required()
                                ->storeFiles(false),
                        ])
                        ->action(function (BankAccount $record, array $data): void {
                            $file = $data['statement'];
                            $csv = $file instanceof TemporaryUploadedFile
                                ? $file->get()
                                : (string) $file;

                            $result = app(BankService::class)->importCsv($record, $csv);

                            Notification::make()
                                ->{$result['errors'] === [] ? 'success' : 'warning'}()
                                ->title("{$result['imported']} imported · {$result['skipped']} duplicates skipped")
                                ->body($result['errors'] === [] ? null : implode("\n", array_slice($result['errors'], 0, 5)))
                                ->persistent()
                                ->send();
                        }),
                    Action::make('reconcile')
                        ->icon('heroicon-o-link')
                        ->visible(function (): bool {
                            $user = Auth::user();

                            return $user instanceof User && $user->can('finance.bank.reconcile');
                        })
                        ->url(fn (BankAccount $record): string => static::getUrl('reconcile', ['record' => $record])),
                    Action::make('balanceCheck')
                        ->label('Bank vs ledger')
                        ->icon('heroicon-o-scale')
                        ->action(function (BankAccount $record): void {
                            $check = app(BankService::class)->balanceComparison($record);
                            $fmt = fn (int $cents): string => Money::ofMinor($cents, $record->currency)->formatToLocale('nl_NL');

                            Notification::make()
                                ->{$check['difference_cents'] === 0 ? 'success' : 'warning'}()
                                ->title($check['difference_cents'] === 0 ? 'Bank and ledger agree' : 'Difference: '.$fmt($check['difference_cents']))
                                ->body("Bank: {$fmt($check['bank_cents'])} · Ledger: {$fmt($check['ledger_cents'])}")
                                ->send();
                        }),
                    EditAction::make()
                        ->visible(function (): bool {
                            $user = Auth::user();

                            return $user instanceof User && $user->can('finance.bank.manage');
                        }),
                ]),
            ])
            ->emptyStateHeading('No bank accounts yet')
            ->emptyStateDescription('Add your business account and import its CSV statements to reconcile.');
    }

    public static function getPages(): array
    {
        return [
            'index' => BankAccountResource\Pages\ListBankAccounts::route('/'),
            'create' => BankAccountResource\Pages\CreateBankAccount::route('/create'),
            'edit' => BankAccountResource\Pages\EditBankAccount::route('/{record}/edit'),
            'reconcile' => BankAccountResource\Pages\ReconcileBankAccount::route('/{record}/reconcile'),
        ];
    }
}
