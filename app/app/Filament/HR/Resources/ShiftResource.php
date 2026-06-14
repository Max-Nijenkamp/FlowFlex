<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\Employee;
use App\Models\HR\Shift;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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
        return $schema->components([
            Section::make('Shift')
                ->columns(2)
                ->components([
                    DatePicker::make('date')->required(),
                    TextInput::make('role')->required()->maxLength(100),
                    TimePicker::make('start_time')->seconds(false)->required(),
                    TimePicker::make('end_time')->seconds(false)->required()->after('start_time'),
                ]),
            Section::make('Assignment')
                ->columns(2)
                ->components([
                    Select::make('employee_id')->label('Assigned to')
                        ->options(fn () => Employee::query()->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->nullable()
                        ->helperText('Leave empty to flag a coverage gap'),
                    Select::make('status')
                        ->options(['draft' => 'Draft', 'published' => 'Published', 'cancelled' => 'Cancelled'])
                        ->default('draft')
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('role'),
                TextColumn::make('employee.full_name')->label('Assigned')->state(fn (Shift $r) => $r->employee_id !== null ? Employee::find($r->employee_id)?->full_name : null)->placeholder('⚠ gap'),
                TextColumn::make('start_time'),
                TextColumn::make('end_time'),
                TextColumn::make('status')->badge(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ShiftResource\Pages\ListShifts::route('/'),
        ];
    }
}
