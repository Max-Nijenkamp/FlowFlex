<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Actions\Finance\RenderCustomerInvoicePdfAction;
use App\Contracts\Finance\InvoiceServiceInterface;
use App\Data\Finance\RecordPaymentData;
use App\Models\Finance\Customer;
use App\Models\Finance\Invoice;
use App\Models\User;
use App\Services\BillingService;
use App\Support\Services\CompanyContext;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Customer invoices (finance.invoicing). Drafts are editable; send
 * assigns the number and posts revenue; payments and voids run through
 * InvoiceService so the ledger stays the record.
 */
class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-euro';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'invoices';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.invoices.view-any')
            && app(BillingService::class)->hasModule('finance.invoicing');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('finance.invoices.create');
    }

    public static function canEdit(mixed $record): bool
    {
        return $record instanceof Invoice && (string) $record->status === 'draft';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invoice')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(fn (): array => Customer::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required(),
                    Select::make('recurring_schedule')
                        ->label('Repeats')
                        ->options(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'annually' => 'Annually'])
                        ->placeholder('One-off'),
                    DatePicker::make('issue_date')->native(false)->default(now()),
                    DatePicker::make('due_date')->native(false)->after('issue_date'),
                    TextInput::make('discount_percent')
                        ->label('Discount (%)')
                        ->numeric()->minValue(0)->maxValue(100)->default(0),
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                    Repeater::make('lines')
                        ->relationship('lines')
                        ->columnSpanFull()
                        ->columns(4)
                        ->defaultItems(1)
                        ->schema([
                            TextInput::make('description')->required()->columnSpan(2),
                            TextInput::make('quantity')->numeric()->minValue(0.01)->default(1)->required(),
                            TextInput::make('unit_price_cents')
                                ->label('Unit price (€)')
                                ->numeric()->minValue(0)->required()
                                ->formatStateUsing(fn (?int $state): string => number_format(($state ?? 0) / 100, 2, '.', ''))
                                ->dehydrateStateUsing(fn (string|int|float|null $state): int => (int) round(((float) ($state ?? 0)) * 100)),
                        ])
                        ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::hydrateLine($data))
                        ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::hydrateLine($data)),
                ]),
        ]);
    }

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    public static function hydrateLine(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();
        $rate = (float) ($data['tax_rate_percent'] ?? 21);
        $net = (int) round(((float) $data['quantity']) * (int) $data['unit_price_cents']);
        $tax = (int) round($net * $rate / 100);

        $data['tax_rate_percent'] = $rate;
        $data['tax_cents'] = $tax;
        $data['line_total_cents'] = $net + $tax;

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->label('Number')->placeholder('draft')->searchable(),
                TextColumn::make('customer.name')->label('Customer')->searchable(),
                TextColumn::make('total_cents')
                    ->label('Total')
                    ->formatStateUsing(fn (Invoice $record): string => Money::ofMinor($record->total_cents, $record->currency)->formatToLocale('nl_NL')),
                TextColumn::make('paid_amount_cents')
                    ->label('Paid')
                    ->formatStateUsing(fn (Invoice $record): string => Money::ofMinor($record->paid_amount_cents, $record->currency)->formatToLocale('nl_NL')),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (Invoice $record): string => str((string) $record->status)->replace('_', ' ')->headline()->toString())
                    ->color(fn (Invoice $record): string => match ((string) $record->status) {
                        'paid' => 'success', 'sent' => 'info', 'partially_paid' => 'warning',
                        'overdue' => 'danger', 'voided' => 'gray', default => 'gray',
                    }),
                TextColumn::make('due_date')->date('d M Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft', 'sent' => 'Sent', 'partially_paid' => 'Partially paid',
                    'paid' => 'Paid', 'overdue' => 'Overdue', 'voided' => 'Voided',
                ]),
                SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->options(fn (): array => Customer::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(fn (Invoice $record): bool => (string) $record->status === 'draft'),
                    Action::make('send')
                        ->icon('heroicon-o-paper-airplane')
                        ->visible(function (Invoice $record): bool {
                            $user = Auth::user();

                            return (string) $record->status === 'draft'
                                && $user instanceof User
                                && $user->can('finance.invoices.send');
                        })
                        ->requiresConfirmation()
                        ->modalDescription('Assigns the invoice number, posts revenue to the ledger, and emails the PDF to the customer.')
                        ->action(function (Invoice $record): void {
                            try {
                                $sent = app(InvoiceServiceInterface::class)->send($record->id);
                                Notification::make()->success()->title("Invoice {$sent->invoice_number} sent")->send();
                            } catch (Throwable $e) {
                                Notification::make()->danger()->title($e->getMessage())->send();
                            }
                        }),
                    Action::make('recordPayment')
                        ->label('Record payment')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->visible(function (Invoice $record): bool {
                            $user = Auth::user();

                            return in_array((string) $record->status, ['sent', 'partially_paid', 'overdue'], true)
                                && $user instanceof User
                                && $user->can('finance.invoices.record-payment');
                        })
                        ->schema([
                            TextInput::make('amount')
                                ->label('Amount (€)')
                                ->numeric()->minValue(0.01)->required()
                                ->helperText(fn (Invoice $record): string => 'Open balance: '.Money::ofMinor($record->openBalanceCents(), $record->currency)->formatToLocale('nl_NL')),
                            Select::make('method')
                                ->options(['bank-transfer' => 'Bank transfer', 'cash' => 'Cash', 'other' => 'Other'])
                                ->default('bank-transfer'),
                            TextInput::make('reference')->maxLength(120),
                        ])
                        ->action(function (Invoice $record, array $data): void {
                            try {
                                $invoice = app(InvoiceServiceInterface::class)->recordPayment(new RecordPaymentData(
                                    invoiceId: $record->id,
                                    amountCents: (int) round(((float) $data['amount']) * 100),
                                    method: $data['method'] ?? null,
                                    reference: $data['reference'] ?? null,
                                ));
                                Notification::make()->success()
                                    ->title((string) $invoice->status === 'paid' ? 'Invoice fully paid 🎉' : 'Payment recorded')
                                    ->send();
                            } catch (Throwable $e) {
                                Notification::make()->danger()->title(self::firstError($e))->send();
                            }
                        }),
                    Action::make('downloadPdf')
                        ->label('PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn (Invoice $record) => response()->streamDownload(
                            function () use ($record): void {
                                echo RenderCustomerInvoicePdfAction::run($record);
                            },
                            ($record->invoice_number ?? 'invoice-draft').'.pdf',
                            ['Content-Type' => 'application/pdf'],
                        )),
                    Action::make('void')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->visible(function (Invoice $record): bool {
                            $user = Auth::user();

                            return in_array((string) $record->status, ['draft', 'sent', 'overdue'], true)
                                && $user instanceof User
                                && $user->can('finance.invoices.void');
                        })
                        ->schema([
                            Textarea::make('reason')->required()->rows(2),
                        ])
                        ->requiresConfirmation()
                        ->action(function (Invoice $record, array $data): void {
                            try {
                                app(InvoiceServiceInterface::class)->void($record->id, $data['reason']);
                                Notification::make()->success()->title('Invoice voided')->send();
                            } catch (Throwable $e) {
                                Notification::make()->danger()->title($e->getMessage())->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading('No invoices yet')
            ->emptyStateDescription('Create one manually — or win a deal in CRM and find its draft here.');
    }

    private static function firstError(Throwable $e): string
    {
        if ($e instanceof ValidationException) {
            return (string) collect($e->errors())->flatten()->first();
        }

        return $e->getMessage();
    }

    public static function getPages(): array
    {
        return [
            'index' => InvoiceResource\Pages\ListInvoices::route('/'),
            'create' => InvoiceResource\Pages\CreateInvoice::route('/create'),
            'edit' => InvoiceResource\Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
