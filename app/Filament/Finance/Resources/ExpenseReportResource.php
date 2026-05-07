<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Enums\NavigationGroup;
use App\Filament\Finance\Resources\ExpenseReportResource\Pages\CreateExpenseReport;
use App\Filament\Finance\Resources\ExpenseReportResource\Pages\EditExpenseReport;
use App\Filament\Finance\Resources\ExpenseReportResource\Pages\ListExpenseReports;
use App\Models\Finance\ExpenseReport;
use App\Models\Tenant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpenseReportResource extends Resource
{
    protected static ?string $model = ExpenseReport::class;

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Expenses->label();
    }

    public static function getModelLabel(): string
    {
        return __('finance.resources.expense_reports.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('finance.resources.expense_reports.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('finance.expense-reports.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('finance.expense-reports.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('finance.expense-reports.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('finance.expense-reports.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('finance.resources.expense_reports.sections.details'))
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Select::make('tenant_id')
                        ->label(__('finance.resources.expense_reports.fields.tenant_id'))
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
                        ->nullable(),

                    Select::make('status')
                        ->options([
                            'draft'     => 'Draft',
                            'submitted' => 'Submitted',
                            'approved'  => 'Approved',
                            'rejected'  => 'Rejected',
                        ])
                        ->default('draft')
                        ->required(),

                    DateTimePicker::make('submitted_at')
                        ->label(__('finance.resources.expense_reports.fields.submitted_at'))
                        ->nullable()
                        ->native(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenant.first_name')
                    ->label(__('finance.resources.expense_reports.columns.employee'))
                    ->getStateUsing(fn (ExpenseReport $record) => $record->tenant
                        ? trim($record->tenant->first_name . ' ' . $record->tenant->last_name)
                        : '—'
                    )
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'info',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('submitted_at')
                    ->label(__('finance.resources.expense_reports.columns.submitted_at'))
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->actions([
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
        return parent::getEloquentQuery()->with('tenant');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListExpenseReports::route('/'),
            'create' => CreateExpenseReport::route('/create'),
            'edit'   => EditExpenseReport::route('/{record}/edit'),
        ];
    }
}
