<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Models\BillingInvoice;
use App\Models\User;
use App\Services\BillingService;
use Brick\Money\Money;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Read-only invoice list (core.billing-engine/monthly-invoicing):
 * BillingService + Stripe own every write — no create/edit/delete.
 */
class BillingInvoiceResource extends Resource
{
    protected static ?string $model = BillingInvoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Invoices';

    protected static ?string $modelLabel = 'invoice';

    protected static ?string $slug = 'billing';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('core.billing.view')
            && app(BillingService::class)->hasModule('core.billing');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('lines');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_start')
                    ->label('Period')
                    ->formatStateUsing(fn (BillingInvoice $record): string => $record->period_start->format('F Y'))
                    ->sortable(),
                TextColumn::make('total_cents')
                    ->label('Total')
                    ->formatStateUsing(fn (BillingInvoice $record): string => Money::ofMinor($record->total_cents, $record->currency)->formatToLocale('nl_NL')),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (BillingInvoice $record): string => str((string) $record->status)->replace('_', ' ')->headline()->toString())
                    ->color(fn (BillingInvoice $record): string => match ((string) $record->status) {
                        'paid' => 'success',
                        'open' => 'info',
                        'past_due' => 'warning',
                        'uncollectible' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('paid_at')->dateTime('d M Y')->placeholder('—'),
            ])
            ->defaultSort('period_start', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft', 'open' => 'Open', 'paid' => 'Paid',
                        'past_due' => 'Past due', 'uncollectible' => 'Uncollectible',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn (BillingInvoice $record): string => 'Invoice '.$record->period_start->format('F Y'))
                    ->infolist([
                        TextEntry::make('period_start')->label('Period')->date('F Y'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('lines')
                            ->label('Lines')
                            ->formatStateUsing(fn (BillingInvoice $record): string => $record->lines
                                ->map(fn ($line): string => "{$line->module_name}: {$line->user_count} × ".Money::ofMinor($line->unit_price_cents, $record->currency)->formatToLocale('nl_NL').' = '.Money::ofMinor($line->line_total_cents, $record->currency)->formatToLocale('nl_NL'))
                                ->implode("\n")),
                        TextEntry::make('total_cents')
                            ->label('Total')
                            ->formatStateUsing(fn (BillingInvoice $record): string => Money::ofMinor($record->total_cents, $record->currency)->formatToLocale('nl_NL')),
                    ]),
            ])
            ->emptyStateHeading('No invoices yet')
            ->emptyStateDescription('Your first invoice lands after the first full month with paid modules.');
    }

    public static function getPages(): array
    {
        return [
            'index' => BillingInvoiceResource\Pages\ListBillingInvoices::route('/'),
        ];
    }
}
