<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Models\Finance\Account;
use App\Models\User;
use App\Services\BillingService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Chart of accounts (finance.ledger). Posted-to accounts cannot be
 * deleted; inactive accounts block new postings but keep history.
 */
class ChartOfAccountsResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string|\UnitEnum|null $navigationGroup = 'Ledger';

    protected static ?string $navigationLabel = 'Chart of accounts';

    protected static ?string $modelLabel = 'account';

    protected static ?string $slug = 'accounts';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.ledger.view')
            && app(BillingService::class)->hasModule('finance.ledger');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('finance.ledger.manage-accounts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('code')->required()->maxLength(10),
                    TextInput::make('name')->required()->maxLength(120),
                    Select::make('type')
                        ->options(collect(Account::TYPES)->mapWithKeys(
                            fn (string $type): array => [$type => ucfirst($type)],
                        )->all())
                        ->required(),
                    Toggle::make('is_active')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'asset' => 'info', 'liability' => 'warning', 'equity' => 'gray',
                        'revenue' => 'success', 'expense' => 'danger', default => 'gray',
                    }),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('lines_count')->label('Postings')->counts('lines'),
            ])
            ->defaultSort('code')
            ->filters([
                SelectFilter::make('type')->options(collect(Account::TYPES)->mapWithKeys(
                    fn (string $type): array => [$type => ucfirst($type)],
                )->all()),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('finance.ledger.manage-accounts');
                    }),
                DeleteAction::make()
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('finance.ledger.manage-accounts');
                    })
                    ->before(function (DeleteAction $action, Account $record): void {
                        if ($record->lines()->exists()) {
                            Notification::make()->danger()
                                ->title('This account has journal postings — deactivate it instead.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->emptyStateHeading('No accounts yet')
            ->emptyStateDescription('Activate the ledger module to seed the default SME chart.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ChartOfAccountsResource\Pages\ListAccounts::route('/'),
            'create' => ChartOfAccountsResource\Pages\CreateAccount::route('/create'),
            'edit' => ChartOfAccountsResource\Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
