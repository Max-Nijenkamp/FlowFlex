<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseReport;
use App\Models\User;
use App\Services\BillingService;
use App\Services\Finance\ExpenseService;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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
 * Expense reports (finance.expenses/expense-reports): group claims for
 * one period, bulk-submit cascades to member drafts.
 */
class ExpenseReportResource extends Resource
{
    protected static ?string $model = ExpenseReport::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Spending';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $modelLabel = 'expense report';

    protected static ?string $slug = 'expense-reports';

    protected static ?int $navigationSort = 3;

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
        return $record instanceof ExpenseReport
            && $record->status === 'draft'
            && $record->user_id === Auth::id();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Report')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('title')->required()->maxLength(160),
                    Select::make('expenses')
                        ->label('Expenses in this report')
                        ->multiple()
                        ->relationship(
                            'expenses',
                            'merchant',
                            fn ($query) => $query->where('user_id', Auth::id())->whereIn('status', ['draft', 'rejected']),
                        )
                        ->getOptionLabelFromRecordUsing(fn (Expense $record): string => $record->merchant.' — '.Money::ofMinor($record->amount_cents, $record->currency)->formatToLocale('nl_NL'))
                        ->helperText('Only your own draft claims can join a report.'),
                    DatePicker::make('period_start')->native(false)->required(),
                    DatePicker::make('period_end')->native(false)->required()->afterOrEqual('period_start'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('submitter.full_name')->label('By'),
                TextColumn::make('period_start')
                    ->label('Period')
                    ->formatStateUsing(fn (ExpenseReport $record): string => $record->period_start->format('d M').' — '.$record->period_end->format('d M Y')),
                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn (ExpenseReport $record): string => Money::ofMinor(
                        (int) $record->expenses()->sum('amount_cents'),
                        'EUR',
                    )->formatToLocale('nl_NL')),
                TextColumn::make('expenses_count')->label('Claims')->counts('expenses'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'info', 'approved' => 'success', 'rejected' => 'danger', default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(fn (ExpenseReport $record): bool => static::canEdit($record)),
                    Action::make('submit')
                        ->icon('heroicon-o-paper-airplane')
                        ->visible(fn (ExpenseReport $record): bool => $record->status === 'draft'
                            && $record->user_id === Auth::id())
                        ->requiresConfirmation()
                        ->modalDescription('Submits the report and every draft claim in it.')
                        ->action(function (ExpenseReport $record): void {
                            app(ExpenseService::class)->submitReport($record);
                            Notification::make()->success()->title('Report submitted')->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('No reports yet')
            ->emptyStateDescription('Bundle a month of claims into one report and submit them together.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ExpenseReportResource\Pages\ListExpenseReports::route('/'),
            'create' => ExpenseReportResource\Pages\CreateExpenseReport::route('/create'),
            'edit' => ExpenseReportResource\Pages\EditExpenseReport::route('/{record}/edit'),
        ];
    }
}
