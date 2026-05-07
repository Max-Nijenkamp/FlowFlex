<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Enums\NavigationGroup;
use App\Filament\Finance\Resources\CreditNoteResource\Pages\CreateCreditNote;
use App\Filament\Finance\Resources\CreditNoteResource\Pages\EditCreditNote;
use App\Filament\Finance\Resources\CreditNoteResource\Pages\ListCreditNotes;
use App\Models\Finance\CreditNote;
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

class CreditNoteResource extends Resource
{
    protected static ?string $model = CreditNote::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Invoices->label();
    }

    public static function getModelLabel(): string
    {
        return __('finance.resources.credit_notes.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('finance.resources.credit_notes.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('finance.credit-notes.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('finance.credit-notes.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('finance.credit-notes.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('finance.credit-notes.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('finance.resources.credit_notes.sections.details'))
                ->schema([
                    Select::make('invoice_id')
                        ->label(__('finance.resources.credit_notes.fields.invoice_id'))
                        ->options(fn () => Invoice::query()->pluck('number', 'id')->toArray())
                        ->nullable()
                        ->searchable(),

                    TextInput::make('number')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('amount')
                        ->numeric()
                        ->required(),

                    Textarea::make('reason')
                        ->nullable()
                        ->rows(3),

                    DatePicker::make('issued_at')
                        ->required()
                        ->native(false),
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

                TextColumn::make('invoice.number')
                    ->label(__('finance.resources.credit_notes.columns.invoice'))
                    ->placeholder('—'),

                TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('issued_at')
                    ->date('d M Y')
                    ->sortable(),
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
        return parent::getEloquentQuery()->with(['invoice']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCreditNotes::route('/'),
            'create' => CreateCreditNote::route('/create'),
            'edit'   => EditCreditNote::route('/{record}/edit'),
        ];
    }
}
