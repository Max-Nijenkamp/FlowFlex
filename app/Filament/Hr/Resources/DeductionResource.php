<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\DeductionResource\Pages\CreateDeduction;
use App\Filament\Hr\Resources\DeductionResource\Pages\EditDeduction;
use App\Filament\Hr\Resources\DeductionResource\Pages\ListDeductions;
use App\Models\Hr\Deduction;
use App\Models\Hr\Employee;
use App\Models\Hr\PayElement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeductionResource extends Resource
{
    protected static ?string $model = Deduction::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-minus-circle';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Payroll->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.deductions.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.deductions.plural');
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
            Section::make(__('hr.resources.deductions.sections.details'))
                ->schema([
                    Select::make('employee_id')
                        ->label(__('hr.resources.deductions.fields.employee'))
                        ->options(
                            fn () => Employee::query()
                                ->where('company_id', auth()->user()?->company_id)
                                ->get()
                                ->mapWithKeys(fn (Employee $e) => [$e->id => $e->full_name ?: $e->id])
                                ->toArray()
                        )
                        ->required()
                        ->searchable(),

                    Select::make('pay_element_id')
                        ->label(__('hr.resources.deductions.fields.pay_element'))
                        ->options(
                            fn () => PayElement::query()
                                ->where('company_id', auth()->user()?->company_id)
                                ->where('is_active', true)
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->nullable()
                        ->searchable(),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('deduction_type')
                        ->label(__('hr.resources.deductions.fields.deduction_type'))
                        ->nullable()
                        ->maxLength(100),

                    TextInput::make('amount')
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    Toggle::make('is_percentage')
                        ->label(__('hr.resources.deductions.fields.is_percentage'))
                        ->default(false),

                    Toggle::make('is_recurring')
                        ->label(__('hr.resources.deductions.fields.is_recurring'))
                        ->default(false),

                    DatePicker::make('effective_from')
                        ->label(__('hr.resources.deductions.fields.effective_from'))
                        ->nullable()
                        ->native(false),

                    DatePicker::make('effective_to')
                        ->label(__('hr.resources.deductions.fields.effective_to'))
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
                    ->label(__('hr.resources.deductions.columns.employee'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('deduction_type')
                    ->label(__('hr.resources.deductions.columns.type'))
                    ->badge()
                    ->placeholder('—'),

                TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                IconColumn::make('is_percentage')
                    ->label(__('hr.resources.deductions.columns.is_percentage'))
                    ->boolean(),

                IconColumn::make('is_recurring')
                    ->label(__('hr.resources.deductions.columns.is_recurring'))
                    ->boolean(),

                TextColumn::make('effective_from')
                    ->label(__('hr.resources.deductions.columns.from'))
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('effective_to')
                    ->label(__('hr.resources.deductions.columns.to'))
                    ->date('d M Y')
                    ->placeholder('—')
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
        return parent::getEloquentQuery()->with(['employee', 'payElement']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDeductions::route('/'),
            'create' => CreateDeduction::route('/create'),
            'edit'   => EditDeduction::route('/{record}/edit'),
        ];
    }
}
