<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\NewsletterSubscriberResource\Pages\ListNewsletterSubscribers;
use App\Models\Marketing\NewsletterSubscriber;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'subscribed'   => 'success',
                        'unsubscribed' => 'warning',
                        'bounced'      => 'danger',
                        default        => 'gray',
                    }),

                TextColumn::make('source')
                    ->color('gray'),

                TextColumn::make('subscribed_at')
                    ->label('Subscribed')
                    ->dateTime('d M Y')
                    ->sortable(),

                IconColumn::make('double_opt_in_confirmed')
                    ->label('Confirmed')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('subscribed_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'subscribed'   => 'Subscribed',
                        'unsubscribed' => 'Unsubscribed',
                        'bounced'      => 'Bounced',
                    ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletterSubscribers::route('/'),
        ];
    }
}
