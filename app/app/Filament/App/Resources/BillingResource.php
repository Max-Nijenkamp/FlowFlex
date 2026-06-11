<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Contracts\Core\BillingServiceInterface;
use App\Models\Core\BillingInvoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Read-only invoice history for the tenant. Activation lives in the marketplace.
 */
class BillingResource extends Resource
{
    protected static ?string $model = BillingInvoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?string $modelLabel = 'invoice';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.billing.view')
            && app(BillingServiceInterface::class)->hasModule('core.billing');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest('period_start'))
            ->columns([
                TextColumn::make('period_start')->date()->label('Period'),
                TextColumn::make('total_cents')
                    ->label('Total')
                    ->formatStateUsing(fn (int $state, BillingInvoice $record) => number_format($state / 100, 2).' '.$record->currency),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'paid' => 'success',
                        'open', 'draft' => 'info',
                        default => 'danger',
                    }),
                TextColumn::make('paid_at')->dateTime()->placeholder('—'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => BillingResource\Pages\ListBillingInvoices::route('/'),
        ];
    }
}
