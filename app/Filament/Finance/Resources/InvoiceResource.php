<?php

namespace App\Filament\Finance\Resources;

use App\Enums\Finance\InvoiceStatus;
use App\Filament\Finance\Enums\NavigationGroup;
use App\Filament\Finance\Resources\InvoiceResource\Pages\CreateInvoice;
use App\Filament\Finance\Resources\InvoiceResource\Pages\EditInvoice;
use App\Filament\Finance\Resources\InvoiceResource\Pages\ListInvoices;
use App\Models\Finance\Invoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Invoices->label();
    }

    public static function getModelLabel(): string
    {
        return __('finance.resources.invoices.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('finance.resources.invoices.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('finance.invoices.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('finance.invoices.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('finance.invoices.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('finance.invoices.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('finance.resources.invoices.sections.details'))
                ->schema([
                    TextInput::make('number')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('contact_id')
                        ->label('Contact ID')
                        ->nullable()
                        ->maxLength(26),

                    DatePicker::make('issue_date')
                        ->required()
                        ->native(false),

                    DatePicker::make('due_date')
                        ->required()
                        ->native(false),

                    Select::make('currency')
                        ->options([
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'USD' => 'USD',
                        ])
                        ->default('EUR')
                        ->required(),

                    Select::make('status')
                        ->options(
                            collect(InvoiceStatus::cases())
                                ->mapWithKeys(fn (InvoiceStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(InvoiceStatus::Draft->value)
                        ->required(),

                    Textarea::make('notes')
                        ->nullable()
                        ->rows(3),
                ]),

            Section::make(__('finance.resources.invoices.sections.pricing'))
                ->schema([
                    Select::make('discount_type')
                        ->options([
                            'percentage' => 'Percentage',
                            'fixed'      => 'Fixed',
                        ])
                        ->nullable(),

                    TextInput::make('discount_value')
                        ->numeric()
                        ->nullable(),

                    TextInput::make('tax_rate')
                        ->numeric()
                        ->default(0)
                        ->suffix('%'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->searchable()
                    ->sortable(),

                // TODO: Add contact() BelongsTo relation on Invoice model pointing to CrmContact,
                //       then change this to TextColumn::make('contact.full_name')
                TextColumn::make('contact_id')
                    ->label(__('finance.resources.invoices.columns.contact'))
                    ->placeholder('—'),

                TextColumn::make('issue_date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('total')
                    ->numeric(decimalPlaces: 2)
                    ->prefix(fn (Invoice $record) => $record->currency . ' ')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?InvoiceStatus $state) => $state?->label())
                    ->color(fn (?InvoiceStatus $state) => $state?->color()),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'edit'   => EditInvoice::route('/{record}/edit'),
        ];
    }
}
