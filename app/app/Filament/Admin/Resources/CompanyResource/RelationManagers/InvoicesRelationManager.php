<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

use App\Models\BillingInvoice;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest('period_start'))
            ->columns([
                TextColumn::make('period_start')->date()->label('Period'),
                TextColumn::make('total_cents')->label('Total')
                    ->money(fn (BillingInvoice $r) => $r->currency, divideBy: 100),
                TextColumn::make('status')
                    ->state(fn (BillingInvoice $r): string => (string) $r->status)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'open' => 'info',
                        'past_due', 'uncollectible' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('paid_at')->dateTime()->toggleable(),
                TextColumn::make('dunning_attempts')->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
