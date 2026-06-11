<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\LeaveType;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Leave';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.leave.manage-types')
            && app(BillingServiceInterface::class)->hasModule('hr.leave');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(100),
            ColorPicker::make('color')->required(),
            TextInput::make('accrual_days_per_year')->numeric()->minValue(0)->required(),
            TextInput::make('carry_over_days')->numeric()->minValue(0)->required(),
            Toggle::make('requires_approval')->default(true),
            Toggle::make('is_paid')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('accrual_days_per_year')->label('Accrual/yr'),
                TextColumn::make('carry_over_days')->label('Carry-over'),
                IconColumn::make('requires_approval')->boolean(),
                IconColumn::make('is_paid')->boolean()->label('Paid'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => LeaveTypeResource\Pages\ListLeaveTypes::route('/'),
            'create' => LeaveTypeResource\Pages\CreateLeaveType::route('/create'),
            'edit' => LeaveTypeResource\Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }
}
