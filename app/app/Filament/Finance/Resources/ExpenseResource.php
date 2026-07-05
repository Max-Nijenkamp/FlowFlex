<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use App\Models\User;
use App\Services\BillingService;
use App\Services\Finance\ExpenseService;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Expense claims (finance.expenses/approval-workflow). Submitters see
 * their own drafts; approvers work the Submitted filter; no self-
 * approval; reimbursement posts the journal.
 */
class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string|\UnitEnum|null $navigationGroup = 'Spending';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.expenses.view-any')
            && app(BillingService::class)->hasModule('finance.expenses');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('finance.expenses.create');
    }

    public static function canEdit(mixed $record): bool
    {
        return $record instanceof Expense
            && in_array((string) $record->status, ['draft', 'rejected'], true)
            && $record->user_id === Auth::id();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Expense')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('merchant')->required()->maxLength(160),
                    TextInput::make('amount_cents')
                        ->label('Amount (€)')
                        ->numeric()->minValue(0.01)->required()
                        ->formatStateUsing(fn (?int $state): string => number_format(($state ?? 0) / 100, 2, '.', ''))
                        ->dehydrateStateUsing(fn (string|int|float|null $state): int => (int) round(((float) ($state ?? 0)) * 100)),
                    Select::make('category_id')
                        ->label('Category')
                        ->options(fn (): array => ExpenseCategory::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->required()
                        ->helperText('Categories carry the policy limit and the ledger account.'),
                    DatePicker::make('expense_date')
                        ->native(false)
                        ->maxDate(now())
                        ->default(now())
                        ->required(),
                    Textarea::make('description')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('merchant')->searchable(),
                TextColumn::make('amount_cents')
                    ->label('Amount')
                    ->formatStateUsing(fn (Expense $record): string => Money::ofMinor($record->amount_cents, $record->currency)->formatToLocale('nl_NL')),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('submitter.full_name')->label('By'),
                TextColumn::make('expense_date')->date('d M Y')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (Expense $record): string => str((string) $record->status)->headline()->toString())
                    ->color(fn (Expense $record): string => match ((string) $record->status) {
                        'approved' => 'success', 'submitted' => 'info', 'rejected' => 'danger',
                        'reimbursed' => 'success', default => 'gray',
                    }),
                IconColumn::make('is_over_limit')
                    ->label('Over limit')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft', 'submitted' => 'Submitted', 'approved' => 'Approved',
                    'rejected' => 'Rejected', 'reimbursed' => 'Reimbursed',
                ]),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(fn (): array => ExpenseCategory::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(fn (Expense $record): bool => static::canEdit($record)),
                    Action::make('submit')
                        ->icon('heroicon-o-paper-airplane')
                        ->visible(fn (Expense $record): bool => in_array((string) $record->status, ['draft', 'rejected'], true)
                            && $record->user_id === Auth::id())
                        ->action(function (Expense $record): void {
                            $expense = app(ExpenseService::class)->submit($record);
                            Notification::make()->success()
                                ->title($expense->is_over_limit ? 'Submitted — flagged over the category limit' : 'Expense submitted')
                                ->send();
                        }),
                    Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(function (Expense $record): bool {
                            $user = Auth::user();

                            return (string) $record->status === 'submitted'
                                && $user instanceof User
                                && $user->can('finance.expenses.approve');
                        })
                        ->requiresConfirmation()
                        ->action(function (Expense $record): void {
                            try {
                                app(ExpenseService::class)->approve($record);
                                Notification::make()->success()->title('Expense approved')->send();
                            } catch (Throwable $e) {
                                Notification::make()->danger()->title(self::firstError($e))->send();
                            }
                        }),
                    Action::make('reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(function (Expense $record): bool {
                            $user = Auth::user();

                            return (string) $record->status === 'submitted'
                                && $user instanceof User
                                && $user->can('finance.expenses.approve');
                        })
                        ->schema([
                            Textarea::make('reason')->required()->rows(2),
                        ])
                        ->action(function (Expense $record, array $data): void {
                            app(ExpenseService::class)->reject($record, $data['reason']);
                            Notification::make()->success()->title('Expense rejected')->send();
                        }),
                    Action::make('reimburse')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->visible(function (Expense $record): bool {
                            $user = Auth::user();

                            return (string) $record->status === 'approved'
                                && $user instanceof User
                                && $user->can('finance.expenses.approve');
                        })
                        ->requiresConfirmation()
                        ->modalDescription('Marks the expense paid out and posts it to the ledger.')
                        ->action(function (Expense $record): void {
                            try {
                                app(ExpenseService::class)->reimburse($record);
                                Notification::make()->success()->title('Expense reimbursed + posted')->send();
                            } catch (Throwable $e) {
                                Notification::make()->danger()->title($e->getMessage())->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading('No expenses yet')
            ->emptyStateDescription('Claim your first expense — approvals and reimbursement run right here.');
    }

    private static function firstError(Throwable $e): string
    {
        if ($e instanceof ValidationException) {
            return (string) collect($e->errors())->flatten()->first();
        }

        return $e->getMessage();
    }

    public static function getPages(): array
    {
        return [
            'index' => ExpenseResource\Pages\ListExpenses::route('/'),
            'create' => ExpenseResource\Pages\CreateExpense::route('/create'),
            'edit' => ExpenseResource\Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
