<?php

namespace App\Filament\Finance\Resources;

use App\Enums\Finance\ExpenseStatus;
use App\Events\Finance\ExpenseApproved;
use App\Events\Finance\ExpenseRejected;
use App\Filament\Finance\Enums\NavigationGroup;
use App\Filament\Finance\Resources\ExpenseResource\Pages\CreateExpense;
use App\Filament\Finance\Resources\ExpenseResource\Pages\EditExpense;
use App\Filament\Finance\Resources\ExpenseResource\Pages\ListExpenses;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Expenses->label();
    }

    public static function getModelLabel(): string
    {
        return __('finance.resources.expenses.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('finance.resources.expenses.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('finance.expenses.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('finance.expenses.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('finance.expenses.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('finance.expenses.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('finance.resources.expenses.sections.details'))
                ->schema([
                    Select::make('tenant_id')
                        ->label(__('finance.resources.expenses.fields.tenant_id'))
                        ->options(
                            fn () => Tenant::query()
                                ->where('company_id', auth()->user()?->company_id)
                                ->get()
                                ->mapWithKeys(fn (Tenant $tenant) => [
                                    $tenant->id => trim($tenant->first_name . ' ' . $tenant->last_name) ?: $tenant->email,
                                ])
                                ->toArray()
                        )
                        ->searchable()
                        ->required(),

                    Select::make('expense_category_id')
                        ->label(__('finance.resources.expenses.fields.expense_category_id'))
                        ->options(fn () => ExpenseCategory::query()->where('is_active', true)->pluck('name', 'id')->toArray())
                        ->nullable()
                        ->searchable(),

                    TextInput::make('description')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('amount')
                        ->numeric()
                        ->required(),

                    Select::make('currency')
                        ->options([
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'USD' => 'USD',
                        ])
                        ->default('EUR')
                        ->required(),

                    DatePicker::make('expense_date')
                        ->required()
                        ->native(false),

                    Select::make('status')
                        ->options(
                            collect(ExpenseStatus::cases())
                                ->mapWithKeys(fn (ExpenseStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(ExpenseStatus::Pending->value)
                        ->required(),

                    TextInput::make('vendor')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('mileage_km')
                        ->numeric()
                        ->nullable()
                        ->label(__('finance.resources.expenses.fields.mileage_km')),

                    Textarea::make('rejection_reason')
                        ->nullable()
                        ->rows(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->searchable(),

                TextColumn::make('tenant.name')
                    ->label(__('finance.resources.expenses.columns.employee')),

                TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->prefix(fn (Expense $record) => $record->currency . ' ')
                    ->sortable(),

                TextColumn::make('expense_date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?ExpenseStatus $state) => $state?->label())
                    ->color(fn (?ExpenseStatus $state) => $state?->color()),

                TextColumn::make('expenseCategory.name')
                    ->label(__('finance.resources.expenses.columns.category'))
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->actions([
                Action::make('approve')
                    ->label(__('finance.resources.expenses.actions.approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Expense $record) => $record->status === ExpenseStatus::Pending && (auth()->user()?->can('finance.expenses.approve') ?? false))
                    ->requiresConfirmation()
                    ->action(function (Expense $record): void {
                        $record->update([
                            'status'      => ExpenseStatus::Approved,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        event(new ExpenseApproved($record));
                    }),

                Action::make('reject')
                    ->label(__('finance.resources.expenses.actions.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Expense $record) => $record->status === ExpenseStatus::Pending && (auth()->user()?->can('finance.expenses.approve') ?? false))
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label(__('finance.resources.expenses.actions.rejection_reason'))
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Expense $record, array $data): void {
                        $record->update([
                            'status'           => ExpenseStatus::Rejected,
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        event(new ExpenseRejected($record));
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['tenant', 'expenseCategory']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListExpenses::route('/'),
            'create' => CreateExpense::route('/create'),
            'edit'   => EditExpense::route('/{record}/edit'),
        ];
    }
}
