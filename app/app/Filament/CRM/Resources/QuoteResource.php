<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Quote;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use UnitEnum;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyEuro;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.quotes.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.quotes');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('quote_number')->placeholder('draft'),
                TextColumn::make('deal.name')->label('Deal')->placeholder('—'),
                TextColumn::make('total_cents')->label('Total')
                    ->formatStateUsing(fn (int $state, Quote $r) => number_format($state / 100, 2).' '.$r->currency),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'accepted' => 'success', 'declined', 'expired' => 'danger',
                        'sent' => 'info', default => 'gray',
                    }),
                TextColumn::make('valid_until')->date()->placeholder('—'),
            ])
            ->recordActions([
                Action::make('send')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->visible(fn (Quote $r) => $r->status === 'draft'
                        && Auth::guard('web')->user()->can('crm.quotes.send'))
                    ->action(function (Quote $record): void {
                        $record->update([
                            'status' => 'sent',
                            'quote_number' => $record->quote_number ?? 'Q-'.now()->year.'-'.Str::padLeft((string) (Quote::withTrashed()->whereNotNull('quote_number')->count() + 1), 3, '0'),
                            'accept_token' => (string) Str::uuid(), // single-use public accept token
                        ]);
                        Notification::make()->success()->title('Quote sent')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => QuoteResource\Pages\ListQuotes::route('/'),
        ];
    }
}
