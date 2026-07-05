<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Models\Hr\LeaveBalance;
use App\Models\Hr\LeaveType;
use App\Models\User;
use App\Services\BillingService;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Leave balances (hr.leave/leave-balances). Read-only ledger — balances
 * move only through the request workflow and the accrual run.
 */
class LeaveBalanceResource extends Resource
{
    protected static ?string $model = LeaveBalance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Leave';

    protected static ?string $navigationLabel = 'Balances';

    protected static ?string $slug = 'leave-balances';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('hr.leave.view-any')
            && app(BillingService::class)->hasModule('hr.leave');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employee', 'leaveType']);
    }

    public static function table(Table $table): Table
    {
        $format = fn (string $value): string => rtrim(rtrim($value, '0'), '.');

        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->state(fn (LeaveBalance $record): string => $record->employee()->first()->full_name ?? '—'),
                TextColumn::make('leaveType.name')->label('Type')->badge(),
                TextColumn::make('year')->sortable(),
                TextColumn::make('allocated_days')
                    ->label('Allocated')
                    ->formatStateUsing(fn (LeaveBalance $record): string => $format((string) $record->allocated_days)),
                TextColumn::make('taken_days')
                    ->label('Taken')
                    ->formatStateUsing(fn (LeaveBalance $record): string => $format((string) $record->taken_days)),
                TextColumn::make('pending_days')
                    ->label('Pending')
                    ->formatStateUsing(fn (LeaveBalance $record): string => $format((string) $record->pending_days)),
                TextColumn::make('remaining')
                    ->label('Remaining')
                    ->state(fn (LeaveBalance $record): string => $format((string) $record->remainingDays()))
                    ->weight('bold'),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                SelectFilter::make('leave_type_id')
                    ->label('Type')
                    ->options(fn (): array => LeaveType::query()->orderBy('name')->pluck('name', 'id')->all()),
                SelectFilter::make('year')
                    ->options(fn (): array => LeaveBalance::query()
                        ->distinct()->pluck('year', 'year')->map(fn ($year) => (string) $year)->all()),
            ])
            ->emptyStateHeading('No balances yet')
            ->emptyStateDescription('Balances appear when leave is requested or the annual accrual runs.');
    }

    public static function getPages(): array
    {
        return [
            'index' => LeaveBalanceResource\Pages\ListLeaveBalances::route('/'),
        ];
    }
}
