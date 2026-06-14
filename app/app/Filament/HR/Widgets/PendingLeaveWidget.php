<?php

declare(strict_types=1);

namespace App\Filament\HR\Widgets;

use App\Models\HR\LeaveRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class PendingLeaveWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.leave.view-any');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Leave requests waiting for approval')
            ->query(
                fn () => LeaveRequest::query()
                    ->with(['employee', 'leaveType'])
                    ->where('status', 'submitted')
                    ->orderBy('start_date'),
            )
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee'),
                TextColumn::make('leaveType.name')->label('Type')->badge(),
                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('end_date')->date(),
                TextColumn::make('days_requested')->label('Days'),
                TextColumn::make('note')->limit(40)->placeholder('—'),
            ])
            ->emptyStateHeading('No pending requests')
            ->emptyStateDescription('Approved and rejected requests live in Leave requests.')
            ->paginated([5, 10]);
    }
}
