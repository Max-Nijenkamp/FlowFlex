<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\CRM\QuoteServiceInterface;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Models\CRM\Quote;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
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
        return $schema->components([
            Select::make('deal_id')->label('Deal')
                ->options(fn () => Deal::query()->pluck('name', 'id'))->nullable(),
            Select::make('contact_id')->label('Contact')
                ->options(fn () => Contact::query()->get()->pluck('full_name', 'id'))->nullable(),
            DatePicker::make('valid_until')->after('today'),
            Repeater::make('lines')
                ->schema([
                    TextInput::make('description')->required(),
                    TextInput::make('quantity')->numeric()->default(1)->minValue(0.01),
                    TextInput::make('unit_price_cents')->numeric()->required()->label('Unit price (cents)'),
                ])
                ->minItems(1)
                ->defaultItems(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
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
                        $quote = app(QuoteServiceInterface::class)->send($record->id);
                        Notification::make()
                            ->success()
                            ->title('Quote sent')
                            ->body('Accept link: '.url('/quotes/accept/'.$quote->accept_token))
                            ->persistent()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => QuoteResource\Pages\ListQuotes::route('/'),
            'create' => QuoteResource\Pages\CreateQuote::route('/create'),
        ];
    }
}
