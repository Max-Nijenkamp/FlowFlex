<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\Finance\ExpenseServiceInterface;
use App\Exceptions\Finance\CannotApproveOwnExpenseException;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|UnitEnum|null $navigationGroup = 'Spending';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.expenses.view-any')
            && app(BillingServiceInterface::class)->hasModule('finance.expenses');
    }

    public static function canCreate(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.expenses.create');
    }

    /** Submitter (user_id) and over-limit flag are set by ExpenseService::submit. */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Expense')
                ->columns(2)
                ->components([
                    Select::make('category_id')->label('Category')
                        ->options(fn () => ExpenseCategory::query()->orderBy('name')->pluck('name', 'id'))
                        ->required(),
                    TextInput::make('amount_cents')->label('Amount (cents)')
                        ->numeric()->integer()->minValue(1)->required(),
                    DatePicker::make('expense_date')->required()->default(now())
                        ->maxDate(now()),
                    TextInput::make('merchant')->required()->maxLength(200),
                    Textarea::make('description')->nullable()->maxLength(2000)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest('expense_date'))
            ->columns([
                TextColumn::make('merchant')->searchable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('amount_cents')->label('Amount')
                    ->formatStateUsing(fn (int $state, Expense $r) => number_format($state / 100, 2).' '.$r->currency),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'approved', 'reimbursed' => 'success',
                        'rejected' => 'danger',
                        'submitted' => 'warning',
                        default => 'gray',
                    }),
                IconColumn::make('is_over_limit')->boolean()->label('Over limit'),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Expense $r) => (string) $r->status === 'draft'),
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheck)->color('success')
                    ->visible(fn (Expense $r) => (string) $r->status === 'submitted'
                        && Auth::guard('web')->user()->can('finance.expenses.approve'))
                    ->requiresConfirmation()
                    ->action(function (Expense $record): void {
                        try {
                            app(ExpenseServiceInterface::class)->approve($record->id);
                            Notification::make()->success()->title('Expense approved')->send();
                        } catch (CannotApproveOwnExpenseException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
                Action::make('reject')
                    ->icon(Heroicon::OutlinedXMark)->color('danger')
                    ->visible(fn (Expense $r) => (string) $r->status === 'submitted'
                        && Auth::guard('web')->user()->can('finance.expenses.approve'))
                    ->schema([Textarea::make('reason')->required()])
                    ->action(function (Expense $record, array $data): void {
                        app(ExpenseServiceInterface::class)->reject($record->id, $data['reason']);
                        Notification::make()->success()->title('Expense rejected')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ExpenseResource\Pages\ListExpenses::route('/'),
        ];
    }
}
