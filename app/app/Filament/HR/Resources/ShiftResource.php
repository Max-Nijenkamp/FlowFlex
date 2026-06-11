<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\Employee;
use App\Models\HR\Shift;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static string|UnitEnum|null $navigationGroup = 'Leave';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.shifts.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.shifts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('role'),
                TextColumn::make('employee.full_name')->label('Assigned')->state(fn (Shift $r) => $r->employee_id !== null ? Employee::find($r->employee_id)?->full_name : null)->placeholder('⚠ gap'),
                TextColumn::make('start_time'),
                TextColumn::make('end_time'),
                TextColumn::make('status')->badge(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ShiftResource\Pages\ListShifts::route('/'),
        ];
    }
}
