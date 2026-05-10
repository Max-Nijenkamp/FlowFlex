<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BillingResource\Pages\ListBillingSubscriptions;
use App\Models\Core\BillingSubscription;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BillingResource extends Resource
{
    protected static ?string $model = BillingSubscription::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-credit-card';
    }

    public static function getNavigationGroup(): string
    {
        return 'Billing';
    }

    public static function getNavigationLabel(): string
    {
        return 'Subscriptions';
    }

    public static function getNavigationSort(): int
    {
        return 10;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withoutGlobalScopes()->with('company'))
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'trialing' => 'info',
                        'past_due' => 'warning',
                        'canceled' => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('monthly_amount')
                    ->label('MRR')
                    ->formatStateUsing(fn ($state) => '€' . number_format((float) $state / 100, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_period_start')
                    ->label('Period start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Period end')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stripe_subscription_id')
                    ->label('Stripe ID')
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? substr($state, 0, 14) . '...'
                        : '—'
                    )
                    ->tooltip(fn ($record) => $record->stripe_subscription_id)
                    ->copyable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'trialing' => 'Trialing',
                        'active'   => 'Active',
                        'past_due' => 'Past due',
                        'canceled' => 'Canceled',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBillingSubscriptions::route('/'),
        ];
    }
}
