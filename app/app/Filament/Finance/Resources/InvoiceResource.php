<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Actions\Finance\GenerateInvoicePdfAction;
use App\Contracts\BillingServiceInterface;
use App\Contracts\Finance\InvoiceServiceInterface;
use App\Data\Finance\RecordPaymentData;
use App\Models\Finance\Customer;
use App\Models\Finance\Invoice;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.invoices.view-any')
            && app(BillingServiceInterface::class)->hasModule('finance.invoicing');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('customer_id')->label('Customer')
                ->options(fn () => Customer::query()->pluck('name', 'id'))
                ->required(),
            DatePicker::make('issue_date')->required()->default(now()),
            DatePicker::make('due_date')->afterOrEqual('issue_date'),
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
            ->modifyQueryUsing(fn ($query) => $query->latest('issue_date'))
            ->columns([
                TextColumn::make('invoice_number')->placeholder('draft')->searchable(),
                TextColumn::make('customer.name')->label('Customer'),
                TextColumn::make('total_cents')->label('Total')
                    ->formatStateUsing(fn (int $state, Invoice $r) => number_format($state / 100, 2).' '.$r->currency),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'paid' => 'success',
                        'overdue', 'voided' => 'danger',
                        'sent', 'partially_paid' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('due_date')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft', 'sent' => 'Sent', 'paid' => 'Paid', 'overdue' => 'Overdue',
                ]),
            ])
            ->recordActions([
                Action::make('send')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->visible(fn (Invoice $r) => (string) $r->status === 'draft'
                        && Auth::guard('web')->user()->can('finance.invoices.send'))
                    ->requiresConfirmation()
                    ->action(function (Invoice $record): void {
                        app(InvoiceServiceInterface::class)->send($record->id);
                        Notification::make()->success()->title('Invoice sent')->send();
                    }),
                Action::make('pdf')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->label('PDF')
                    ->visible(fn (Invoice $r) => (string) $r->status !== 'draft')
                    ->action(function (Invoice $record) {
                        // Throttled per security notes: 10 renders/min per user.
                        $key = 'invoice-pdf:'.Auth::guard('web')->id();
                        if (RateLimiter::tooManyAttempts($key, 10)) {
                            Notification::make()->danger()->title('Too many PDF requests — wait a minute.')->send();

                            return null;
                        }
                        RateLimiter::hit($key, 60);

                        $path = GenerateInvoicePdfAction::run($record->id);

                        return response()->download(
                            Storage::path($path),
                            basename($path),
                        );
                    }),
                Action::make('recordPayment')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->visible(fn (Invoice $r) => in_array((string) $r->status, ['sent', 'partially_paid', 'overdue'], true)
                        && Auth::guard('web')->user()->can('finance.invoices.record-payment'))
                    ->schema([
                        TextInput::make('amount_cents')->numeric()->required()->label('Amount (cents)'),
                        DatePicker::make('payment_date')->required()->default(now()),
                        Select::make('payment_method')
                            ->options(['bank-transfer' => 'Bank transfer', 'stripe' => 'Stripe', 'cash' => 'Cash', 'other' => 'Other'])
                            ->required(),
                    ])
                    ->action(function (Invoice $record, array $data): void {
                        try {
                            app(InvoiceServiceInterface::class)->recordPayment(RecordPaymentData::from([
                                'invoice_id' => $record->id, ...$data,
                            ]));
                            Notification::make()->success()->title('Payment recorded')->send();
                        } catch (ValidationException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => InvoiceResource\Pages\ListInvoices::route('/'),
            'create' => InvoiceResource\Pages\CreateInvoice::route('/create'),
        ];
    }
}
