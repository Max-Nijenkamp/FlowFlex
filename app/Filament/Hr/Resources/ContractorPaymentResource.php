<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\ContractorPaymentResource\Pages\CreateContractorPayment;
use App\Filament\Hr\Resources\ContractorPaymentResource\Pages\EditContractorPayment;
use App\Filament\Hr\Resources\ContractorPaymentResource\Pages\ListContractorPayments;
use App\Models\Hr\ContractorPayment;
use App\Models\Hr\Employee;
use App\Models\Hr\PayRun;
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

class ContractorPaymentResource extends Resource
{
    protected static ?string $model = ContractorPayment::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Payroll->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.contractor_payments.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.contractor_payments.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.payroll.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.payroll.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.payroll.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.payroll.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('hr.resources.contractor_payments.sections.details'))
                ->schema([
                    Select::make('employee_id')
                        ->label(__('hr.resources.contractor_payments.fields.contractor_employee'))
                        ->options(
                            fn () => Employee::query()
                                ->where('company_id', auth()->user()?->company_id)
                                ->get()
                                ->mapWithKeys(fn (Employee $e) => [$e->id => $e->full_name ?: $e->id])
                                ->toArray()
                        )
                        ->required()
                        ->searchable(),

                    Select::make('pay_run_id')
                        ->label(__('hr.resources.contractor_payments.fields.pay_run'))
                        ->options(
                            fn () => PayRun::query()
                                ->where('company_id', auth()->user()?->company_id)
                                ->pluck('reference', 'id')
                                ->toArray()
                        )
                        ->nullable()
                        ->searchable(),

                    TextInput::make('amount')
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    Select::make('currency')
                        ->options([
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'USD' => 'USD',
                        ])
                        ->default('EUR')
                        ->required(),

                    TextInput::make('reference')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('status')
                        ->nullable()
                        ->maxLength(100),

                    DateTimePicker::make('processed_at')
                        ->nullable()
                        ->native(false),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label(__('hr.resources.contractor_payments.columns.contractor'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payRun.reference')
                    ->label(__('hr.resources.contractor_payments.columns.pay_run'))
                    ->placeholder('—'),

                TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('currency')
                    ->badge(),

                TextColumn::make('status')
                    ->badge()
                    ->placeholder('—'),

                TextColumn::make('processed_at')
                    ->label(__('hr.resources.contractor_payments.columns.processed'))
                    ->dateTime('d M Y H:i')
                    ->placeholder('Pending')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([25, 50])
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
        return parent::getEloquentQuery()->with(['employee', 'payRun']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListContractorPayments::route('/'),
            'create' => CreateContractorPayment::route('/create'),
            'edit'   => EditContractorPayment::route('/{record}/edit'),
        ];
    }
}
