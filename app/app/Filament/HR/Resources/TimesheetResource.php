<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Exceptions\HR\CannotApproveOwnRequestException;
use App\Models\HR\Timesheet;
use App\Services\HR\TimeService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Leave';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.time.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.time');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')->state(fn (Timesheet $r) => $r->employee->full_name ?? '—'),
                TextColumn::make('week_start')->date(),
                TextColumn::make('total_minutes')->label('Hours')->formatStateUsing(fn (int $state) => round($state / 60, 1)),
                TextColumn::make('status')->badge(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheck)->color('success')
                    ->visible(fn (Timesheet $r) => (string) $r->status === 'submitted'
                        && Auth::guard('web')->user()->can('hr.time.approve'))
                    ->requiresConfirmation()
                    ->action(function (Timesheet $record): void {
                        try {
                            app(TimeService::class)->approve($record->id);
                            Notification::make()->success()->title('Timesheet approved')->send();
                        } catch (CannotApproveOwnRequestException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => TimesheetResource\Pages\ListTimesheets::route('/'),
        ];
    }
}
