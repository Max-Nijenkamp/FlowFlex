<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Models\Finance\FiscalPeriod;
use App\Models\User;
use App\Services\BillingService;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Fiscal period locks (finance.ledger/fiscal-period-lock). Closing
 * blocks postings dated in the period; reopening is owner-level and
 * always audited.
 */
class FiscalPeriodResource extends Resource
{
    protected static ?string $model = FiscalPeriod::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';

    protected static string|\UnitEnum|null $navigationGroup = 'Ledger';

    protected static ?string $navigationLabel = 'Fiscal periods';

    protected static ?string $modelLabel = 'fiscal period';

    protected static ?string $slug = 'fiscal-periods';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.ledger.manage-periods')
            && app(BillingService::class)->hasModule('finance.ledger');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => $state === 'open' ? 'success' : 'gray'),
                TextColumn::make('closedBy.full_name')->label('Closed by')->placeholder('—'),
                TextColumn::make('closed_at')->dateTime('d M Y · H:i')->placeholder('—'),
            ])
            ->defaultSort('period', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Add period')
                    ->schema([
                        TextInput::make('period')
                            ->placeholder('2026-07')
                            ->regex('/^\d{4}-(0[1-9]|1[0-2])$/')
                            ->required(),
                    ])
                    ->mutateDataUsing(function (array $data): array {
                        $data['company_id'] = app(CompanyContext::class)->currentId();
                        $data['status'] = 'open';

                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('close')
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->visible(fn (FiscalPeriod $record): bool => $record->status === 'open')
                    ->requiresConfirmation()
                    ->modalDescription('Nothing can be posted into a closed period until it is reopened.')
                    ->action(function (FiscalPeriod $record): void {
                        $record->update(['status' => 'closed', 'closed_by' => Auth::id(), 'closed_at' => now()]);

                        $causer = Auth::user();
                        app(AuditLogger::class)->log('finance.period-closed', $record, $causer instanceof User ? $causer : null, ['period' => $record->period]);
                        Notification::make()->success()->title("Period {$record->period} closed")->send();
                    }),
                Action::make('reopen')
                    ->icon('heroicon-o-lock-open')
                    ->color('danger')
                    ->visible(function (FiscalPeriod $record): bool {
                        $user = Auth::user();

                        // Reopening rewrites history — owner only.
                        return $record->status === 'closed'
                            && $user instanceof User
                            && $user->hasRole('owner');
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Reopening allows retroactive postings — this is audited.')
                    ->action(function (FiscalPeriod $record): void {
                        $record->update(['status' => 'open', 'closed_by' => null, 'closed_at' => null]);

                        $causer = Auth::user();
                        app(AuditLogger::class)->log('finance.period-reopened', $record, $causer instanceof User ? $causer : null, ['period' => $record->period]);
                        Notification::make()->success()->title("Period {$record->period} reopened")->send();
                    }),
            ])
            ->emptyStateHeading('No period locks yet')
            ->emptyStateDescription('Add a period and close it to freeze that month against edits.');
    }

    public static function getPages(): array
    {
        return [
            'index' => FiscalPeriodResource\Pages\ListFiscalPeriods::route('/'),
        ];
    }
}
