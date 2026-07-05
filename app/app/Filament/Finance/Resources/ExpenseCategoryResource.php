<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Models\Finance\Account;
use App\Models\Finance\ExpenseCategory;
use App\Models\User;
use App\Services\BillingService;
use Brick\Money\Money;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Expense categories = the expense policy (finance.expenses/expense-
 * policy): per-transaction limit + GL posting target.
 */
class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Spending';

    protected static ?string $navigationLabel = 'Categories';

    protected static ?string $modelLabel = 'expense category';

    protected static ?string $slug = 'expense-categories';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.expenses.manage-policy')
            && app(BillingService::class)->hasModule('finance.expenses');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(120),
                    TextInput::make('limit_per_transaction_cents')
                        ->label('Limit per transaction (€)')
                        ->numeric()->minValue(0)
                        ->helperText('Empty = no limit; claims above it get flagged, not blocked.')
                        ->formatStateUsing(fn (?int $state): ?string => $state === null ? null : number_format($state / 100, 2, '.', ''))
                        ->dehydrateStateUsing(fn (string|int|float|null $state): ?int => $state === null || $state === ''
                            ? null
                            : (int) round(((float) $state) * 100)),
                    Select::make('gl_account_id')
                        ->label('Posts to ledger account')
                        ->options(fn (): array => Account::query()
                            ->where('type', 'expense')
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
                TextColumn::make('limit_per_transaction_cents')
                    ->label('Limit')
                    ->formatStateUsing(fn (?int $state): string => $state === null
                        ? 'No limit'
                        : Money::ofMinor($state, 'EUR')->formatToLocale('nl_NL'))
                    ->placeholder('No limit'),
                TextColumn::make('glAccount.code')->label('Ledger account'),
                TextColumn::make('expenses_count')->label('Expenses')->counts('expenses'),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, ExpenseCategory $record): void {
                        if ($record->expenses()->exists()) {
                            Notification::make()->danger()
                                ->title('This category has expenses — it cannot be removed.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->emptyStateHeading('No categories yet')
            ->emptyStateDescription('Create categories with limits and ledger targets — they are your expense policy.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ExpenseCategoryResource\Pages\ListExpenseCategories::route('/'),
            'create' => ExpenseCategoryResource\Pages\CreateExpenseCategory::route('/create'),
            'edit' => ExpenseCategoryResource\Pages\EditExpenseCategory::route('/{record}/edit'),
        ];
    }
}
