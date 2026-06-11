<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\PayrollEmployee;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PayrollEmployeeResource extends Resource
{
    protected static ?string $model = PayrollEmployee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $modelLabel = 'payroll profile';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.payroll.view-sensitive')
            && app(BillingServiceInterface::class)->hasModule('hr.payroll');
    }

    public static function canCreate(): bool
    {
        return false; // rows created by the EmployeeHired listener
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('salary_raw')->label('Monthly gross (cents)')->numeric()->minValue(0),
            TextInput::make('iban')->label('IBAN'),
            Select::make('pay_type')->options(['salaried' => 'Salaried', 'hourly' => 'Hourly'])->required(),
            Select::make('status')->options(['incomplete' => 'Incomplete', 'ready' => 'Ready'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')
                    ->state(fn (PayrollEmployee $r) => $r->employee->full_name),
                TextColumn::make('pay_type')->badge(),
                TextColumn::make('status')->badge()
                    ->color(fn ($state) => $state === 'ready' ? 'success' : 'warning'),
                TextColumn::make('final_pay_flagged')->label('Final pay')
                    ->formatStateUsing(fn (bool $state) => $state ? 'flagged' : '—'),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => PayrollEmployeeResource\Pages\ListPayrollEmployees::route('/'),
            'edit' => PayrollEmployeeResource\Pages\EditPayrollEmployee::route('/{record}/edit'),
        ];
    }
}
