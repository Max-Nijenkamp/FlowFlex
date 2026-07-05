<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\Finance\LedgerServiceInterface;
use App\Models\Finance\JournalEntry;
use App\Models\User;
use App\Services\BillingService;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Journal browser (finance.ledger). Posted entries are immutable — no
 * edit/delete surface exists; the only correction is a mirrored
 * reversal. Manual entries post through TrialBalancePage's action.
 */
class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Ledger';

    protected static ?string $navigationLabel = 'Journal';

    protected static ?string $modelLabel = 'journal entry';

    protected static ?string $slug = 'journal';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.ledger.view')
            && app(BillingService::class)->hasModule('finance.ledger');
    }

    public static function canCreate(): bool
    {
        return false; // LedgerService::post is the only write path
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entry_date')->label('Date')->date('d M Y')->sortable(),
                TextColumn::make('reference')->searchable(),
                TextColumn::make('description')->limit(48)->searchable(),
                TextColumn::make('source_type')
                    ->label('Source')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === null ? 'manual' : str($state)->replace('-', ' ')->toString())
                    ->color('gray'),
                TextColumn::make('total')
                    ->label('Amount')
                    ->state(fn (JournalEntry $record): string => Money::ofMinor(
                        (int) $record->lines()->sum('debit_cents'),
                        'EUR',
                    )->formatToLocale('nl_NL')),
            ])
            ->defaultSort('entry_date', 'desc')
            ->filters([
                SelectFilter::make('source_type')
                    ->label('Source')
                    ->options([
                        'invoice' => 'Invoice', 'invoice-payment' => 'Invoice payment',
                        'expense' => 'Expense', 'reversal' => 'Reversal',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn (JournalEntry $record): string => $record->reference)
                    ->modalWidth('lg')
                    ->modalContent(fn (JournalEntry $record) => view('filament.finance.journal-entry', ['record' => $record])),
                Action::make('reverse')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible(function (JournalEntry $record): bool {
                        $user = Auth::user();

                        return $record->source_type !== 'reversal'
                            && $user instanceof User
                            && $user->can('finance.ledger.reverse');
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Posts a mirror entry that cancels this one — the original stays untouched.')
                    ->action(function (JournalEntry $record): void {
                        try {
                            app(LedgerServiceInterface::class)->reverse($record->id);
                            Notification::make()->success()->title('Reversal posted')->send();
                        } catch (Throwable $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ])
            ->emptyStateHeading('No journal entries yet')
            ->emptyStateDescription('Send an invoice or approve an expense and the ledger fills itself.');
    }

    public static function getPages(): array
    {
        return [
            'index' => JournalEntryResource\Pages\ListJournalEntries::route('/'),
        ];
    }
}
