<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

use App\Models\BillingInvoice;
use Brick\Money\Money;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Read-only invoice tab per company (core.staff-console/billing-overview). */
class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Invoices';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_start')
                    ->label('Period')
                    ->formatStateUsing(fn (BillingInvoice $record): string => $record->period_start->format('F Y')),
                TextColumn::make('total_cents')
                    ->label('Total')
                    ->formatStateUsing(fn (BillingInvoice $record): string => Money::ofMinor($record->total_cents, $record->currency)->formatToLocale('nl_NL')),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (BillingInvoice $record): string => str((string) $record->status)->replace('_', ' ')->headline()->toString()),
                TextColumn::make('paid_at')->dateTime('d M Y')->placeholder('-'),
            ])
            ->defaultSort('period_start', 'desc');
    }
}
