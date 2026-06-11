<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\HR\PayrollServiceInterface;
use App\Exceptions\HR\CannotApproveOwnRunException;
use App\Exceptions\HR\IncompletePayrollProfileException;
use App\Models\HR\PayrollRun;
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

class PayrollRunResource extends Resource
{
    protected static ?string $model = PayrollRun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Payroll';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.payroll.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.payroll');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest('period_start'))
            ->columns([
                TextColumn::make('period_start')->date()->label('Period'),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'approved' => 'success',
                        'archived' => 'gray',
                        'processing' => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('total_gross_cents')->label('Gross')
                    ->formatStateUsing(fn (int $state, PayrollRun $r) => number_format($state / 100, 2).' '.$r->currency),
                TextColumn::make('total_net_cents')->label('Net')
                    ->formatStateUsing(fn (int $state, PayrollRun $r) => number_format($state / 100, 2).' '.$r->currency),
                TextColumn::make('payslips_count')->counts('payslips')->label('Payslips'),
            ])
            ->recordActions([
                Action::make('process')
                    ->icon(Heroicon::OutlinedPlay)
                    ->visible(fn (PayrollRun $r) => (string) $r->status === 'draft'
                        && Auth::guard('web')->user()->can('hr.payroll.process'))
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record): void {
                        try {
                            app(PayrollServiceInterface::class)->processRun($record->id);
                            Notification::make()->success()->title('Payslips generated')->send();
                        } catch (IncompletePayrollProfileException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->visible(fn (PayrollRun $r) => (string) $r->status === 'processing'
                        && Auth::guard('web')->user()->can('hr.payroll.approve'))
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record): void {
                        try {
                            app(PayrollServiceInterface::class)->approveRun($record->id);
                            Notification::make()->success()->title('Payroll run approved')->send();
                        } catch (CannotApproveOwnRunException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => PayrollRunResource\Pages\ListPayrollRuns::route('/'),
        ];
    }
}
