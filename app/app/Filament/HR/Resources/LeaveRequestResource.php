<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\HR\LeaveServiceInterface;
use App\Exceptions\HR\CannotApproveOwnRequestException;
use App\Models\HR\Employee;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Leave';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.leave.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.leave');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('employee_id')->label('Employee')
                ->options(fn () => Employee::query()->get()->pluck('full_name', 'id'))
                ->required(),
            Select::make('leave_type_id')->label('Type')
                ->options(fn () => LeaveType::query()->pluck('name', 'id'))
                ->required(),
            DatePicker::make('start_date')->required(),
            DatePicker::make('end_date')->required()->afterOrEqual('start_date'),
            Textarea::make('note')->maxLength(1000),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')
                    ->state(fn (LeaveRequest $r) => $r->employee->full_name),
                TextColumn::make('leaveType.name')->label('Type')->badge(),
                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('end_date')->date(),
                TextColumn::make('days_requested')->label('Days'),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'approved' => 'success',
                        'rejected', 'cancelled' => 'danger',
                        'submitted' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'submitted' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected',
                ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->visible(fn (LeaveRequest $r) => (string) $r->status === 'submitted'
                        && Auth::guard('web')->user()->can('hr.leave.approve'))
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record): void {
                        try {
                            app(LeaveServiceInterface::class)->approve($record->id);
                            Notification::make()->success()->title('Leave approved')->send();
                        } catch (CannotApproveOwnRequestException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
                Action::make('reject')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->visible(fn (LeaveRequest $r) => (string) $r->status === 'submitted'
                        && Auth::guard('web')->user()->can('hr.leave.reject'))
                    ->schema([Textarea::make('rejection_reason')->required()->maxLength(1000)])
                    ->action(function (LeaveRequest $record, array $data): void {
                        app(LeaveServiceInterface::class)->reject($record->id, $data['rejection_reason']);
                        Notification::make()->success()->title('Leave rejected')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => LeaveRequestResource\Pages\ListLeaveRequests::route('/'),
            'create' => LeaveRequestResource\Pages\CreateLeaveRequest::route('/create'),
        ];
    }
}
