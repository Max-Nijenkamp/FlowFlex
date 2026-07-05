<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Data\Hr\SubmitLeaveRequestData;
use App\Exceptions\Hr\LeaveOverlapException;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveRequest;
use App\Models\Hr\LeaveType;
use App\Models\User;
use App\Services\BillingService;
use App\Services\Hr\LeaveService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Leave requests (hr.leave/leave-request-workflow). Creation happens
 * through the header modal → LeaveService::submit (working days,
 * overlap check, balance move); approvers decide from the list.
 */
class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Leave';

    protected static ?string $navigationLabel = 'Requests';

    protected static ?string $slug = 'leave-requests';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('hr.leave.view-any')
            && app(BillingService::class)->hasModule('hr.leave');
    }

    public static function canCreate(): bool
    {
        return false; // the header "Request leave" modal owns creation
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->state(fn (LeaveRequest $record): string => $record->employee()->first()->full_name ?? '—'),
                TextColumn::make('leaveType.name')->label('Type')->badge(),
                TextColumn::make('start_date')
                    ->label('Period')
                    ->formatStateUsing(fn (LeaveRequest $record): string => $record->start_date->format('d M').' — '.$record->end_date->format('d M Y')),
                TextColumn::make('days_requested')
                    ->label('Days')
                    ->formatStateUsing(fn (LeaveRequest $record): string => rtrim(rtrim((string) $record->days_requested, '0'), '.')),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (LeaveRequest $record): string => str((string) $record->status)->headline()->toString())
                    ->color(fn (LeaveRequest $record): string => match ((string) $record->status) {
                        'approved' => 'success', 'submitted' => 'info', 'rejected' => 'danger', default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'submitted' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled',
                ]),
                SelectFilter::make('leave_type_id')
                    ->label('Type')
                    ->options(fn (): array => LeaveType::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->headerActions([
                Action::make('request')
                    ->label('Request leave')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('hr.leave.create');
                    })
                    ->schema([
                        Select::make('employee_id')
                            ->label('Employee')
                            ->options(fn (): array => Employee::query()
                                ->where('status', '!=', 'terminated')
                                ->get()
                                ->sortBy('last_name')
                                ->mapWithKeys(fn (Employee $employee): array => [$employee->id => $employee->full_name])
                                ->all())
                            ->searchable()
                            ->required(),
                        Select::make('leave_type_id')
                            ->label('Type')
                            ->options(fn (): array => LeaveType::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->required(),
                        DatePicker::make('start_date')->native(false)->required(),
                        DatePicker::make('end_date')->native(false)->required()->afterOrEqual('start_date'),
                        Textarea::make('note')->rows(2),
                    ])
                    ->action(function (array $data): void {
                        try {
                            $request = app(LeaveService::class)->submit(new SubmitLeaveRequestData(
                                employeeId: $data['employee_id'],
                                leaveTypeId: $data['leave_type_id'],
                                startDate: $data['start_date'],
                                endDate: $data['end_date'],
                                note: $data['note'] ?? null,
                            ));
                            Notification::make()->success()
                                ->title((string) $request->status === 'approved' ? 'Leave auto-approved' : 'Request submitted')
                                ->send();
                        } catch (LeaveOverlapException $e) {
                            Notification::make()->warning()->title($e->getMessage())->send();
                        } catch (Throwable $e) {
                            Notification::make()->danger()->title(self::firstError($e))->send();
                        }
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(function (LeaveRequest $record): bool {
                            $user = Auth::user();

                            return (string) $record->status === 'submitted'
                                && $user instanceof User
                                && $user->can('hr.leave.approve');
                        })
                        ->requiresConfirmation()
                        ->action(function (LeaveRequest $record): void {
                            app(LeaveService::class)->approve($record);
                            Notification::make()->success()->title('Leave approved')->send();
                        }),
                    Action::make('reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(function (LeaveRequest $record): bool {
                            $user = Auth::user();

                            return (string) $record->status === 'submitted'
                                && $user instanceof User
                                && $user->can('hr.leave.reject');
                        })
                        ->schema([
                            Textarea::make('reason')->required()->rows(2),
                        ])
                        ->action(function (LeaveRequest $record, array $data): void {
                            app(LeaveService::class)->reject($record, $data['reason']);
                            Notification::make()->success()->title('Leave rejected')->send();
                        }),
                    Action::make('cancel')
                        ->icon('heroicon-o-no-symbol')
                        ->visible(fn (LeaveRequest $record): bool => in_array((string) $record->status, ['submitted', 'approved'], true))
                        ->requiresConfirmation()
                        ->action(function (LeaveRequest $record): void {
                            app(LeaveService::class)->cancel($record);
                            Notification::make()->success()->title('Request cancelled')->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('No leave requests yet')
            ->emptyStateDescription('Request leave from the button above — approvals happen right here.');
    }

    private static function firstError(Throwable $e): string
    {
        if ($e instanceof ValidationException) {
            return (string) collect($e->errors())->flatten()->first();
        }

        return $e->getMessage();
    }

    public static function getPages(): array
    {
        return [
            'index' => LeaveRequestResource\Pages\ListLeaveRequests::route('/'),
        ];
    }
}
