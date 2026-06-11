<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BillingInvoiceResource\Pages;
use App\Models\BillingInvoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/** Read-only cross-company invoice overview (core.staff-console). */
class BillingInvoiceResource extends Resource
{
    protected static ?string $model = BillingInvoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?string $modelLabel = 'invoice';

    public static function canAccess(): bool
    {
        return Auth::guard('admin')->check();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->modifyQueryUsing(fn ($query) => $query->with('company')->latest('period_start'))
            ->columns([
                TextColumn::make('company.name')->searchable()->sortable(),
                TextColumn::make('period_start')->date()->label('Period')->sortable(),
                TextColumn::make('total_cents')->label('Total')
                    ->money(fn (BillingInvoice $r) => $r->currency, divideBy: 100)
                    ->sortable(),
                TextColumn::make('status')
                    ->state(fn (BillingInvoice $r): string => (string) $r->status)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'open' => 'info',
                        'past_due', 'uncollectible' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('paid_at')->dateTime()->sortable()->toggleable(),
                TextColumn::make('dunning_attempts')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'open' => 'Open',
                    'paid' => 'Paid',
                    'past_due' => 'Past due',
                    'uncollectible' => 'Uncollectible',
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillingInvoices::route('/'),
        ];
    }
}
