<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Models\Hr\LeaveType;
use App\Models\User;
use App\Services\BillingService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/** Leave types (hr.leave/leave-types): accrual, carry-over, approval, colour. */
class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static string|\UnitEnum|null $navigationGroup = 'Leave';

    protected static ?string $navigationLabel = 'Leave types';

    protected static ?string $slug = 'leave-types';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('hr.leave.manage-types')
            && app(BillingService::class)->hasModule('hr.leave');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Leave type')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(120),
                    ColorPicker::make('color')->default('#4ADE80'),
                    TextInput::make('accrual_days_per_year')
                        ->label('Days per year')
                        ->numeric()->minValue(0)->maxValue(365)->default(25)
                        ->helperText('0 = no accrual (e.g. unpaid leave).'),
                    TextInput::make('carry_over_days')
                        ->label('Carry-over cap (days)')
                        ->numeric()->minValue(0)->maxValue(365)->default(5),
                    Toggle::make('requires_approval')
                        ->default(true)
                        ->helperText('Off = requests auto-approve on submit.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('accrual_days_per_year')
                    ->label('Days/year')
                    ->formatStateUsing(fn (LeaveType $record): string => rtrim(rtrim((string) $record->accrual_days_per_year, '0'), '.')),
                TextColumn::make('carry_over_days')->label('Carry-over'),
                IconColumn::make('requires_approval')->label('Approval')->boolean(),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, LeaveType $record): void {
                        if ($record->requests()->exists()) {
                            Notification::make()->danger()
                                ->title('This type has leave requests — it cannot be removed.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->emptyStateHeading('No leave types yet')
            ->emptyStateDescription('Create types like Holiday, Sick, or Unpaid to open up requests.');
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
