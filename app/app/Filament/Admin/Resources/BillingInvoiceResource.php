<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Actions\RenderInvoicePdfAction;
use App\Filament\Admin\Resources\BillingInvoiceResource\Pages\ListBillingInvoices;
use App\Models\BillingInvoice;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Cross-company billing overview for staff (core.staff-console/
 * billing-overview). Read-only; CompanyScope no-ops under the admin guard.
 */
class BillingInvoiceResource extends Resource
{
    protected static ?string $model = BillingInvoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Billing';

    protected static ?string $modelLabel = 'invoice';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('company');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Company')->searchable()->sortable(),
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
                        'paid' => 'success', 'open' => 'info', 'past_due' => 'warning', 'uncollectible' => 'danger', default => 'gray',
                    }),
            ])
            ->defaultSort('period_start', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft', 'open' => 'Open', 'paid' => 'Paid', 'past_due' => 'Past due', 'uncollectible' => 'Uncollectible',
                ]),
            ])
            ->recordActions([
                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (BillingInvoice $record) => response()->streamDownload(
                        function () use ($record): void {
                            echo RenderInvoicePdfAction::run($record);
                        },
                        RenderInvoicePdfAction::number($record).'.pdf',
                        ['Content-Type' => 'application/pdf'],
                    )),
            ])
            ->emptyStateHeading('No invoices yet');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBillingInvoices::route('/'),
        ];
    }
}
