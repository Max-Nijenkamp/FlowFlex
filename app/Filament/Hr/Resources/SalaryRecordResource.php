<?php

namespace App\Filament\Hr\Resources;

use App\Enums\Hr\PayFrequency;
use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\SalaryRecordResource\Pages\CreateSalaryRecord;
use App\Filament\Hr\Resources\SalaryRecordResource\Pages\EditSalaryRecord;
use App\Filament\Hr\Resources\SalaryRecordResource\Pages\ListSalaryRecords;
use App\Models\Hr\Employee;
use App\Models\Hr\SalaryRecord;
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

class SalaryRecordResource extends Resource
{
    protected static ?string $model = SalaryRecord::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Payroll;

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.salary-records.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.salary-records.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.salary-records.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.salary-records.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Salary Details')
                ->schema([
                    Select::make('employee_id')
                        ->label('Employee')
                        ->options(fn () => Employee::query()->get()->mapWithKeys(fn (Employee $e) => [$e->id => trim("{$e->first_name} {$e->last_name}")])->toArray())
                        ->required()
                        ->searchable(),

                    DatePicker::make('effective_date')
                        ->required()
                        ->native(false),

                    TextInput::make('salary')
                        ->label('Annual Salary')
                        ->numeric()
                        ->required(),

                    TextInput::make('currency')
                        ->default('GBP')
                        ->maxLength(3),

                    Select::make('pay_frequency')
                        ->options(
                            collect(PayFrequency::cases())
                                ->mapWithKeys(fn (PayFrequency $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->required(),

                    Textarea::make('notes')
                        ->nullable()
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_name')
                    ->label('Employee')
                    ->getStateUsing(fn (SalaryRecord $record) => trim("{$record->employee->first_name} {$record->employee->last_name}")),

                TextColumn::make('effective_date')
                    ->label('Effective Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('pay_frequency')
                    ->label('Frequency')
                    ->badge()
                    ->formatStateUsing(fn (?PayFrequency $state) => $state?->label()),

                TextColumn::make('currency')
                    ->label('Currency'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('effective_date', 'desc')
            ->striped()
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['employee']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSalaryRecords::route('/'),
            'create' => CreateSalaryRecord::route('/create'),
            'edit'   => EditSalaryRecord::route('/{record}/edit'),
        ];
    }
}
