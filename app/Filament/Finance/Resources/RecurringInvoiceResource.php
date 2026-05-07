<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Enums\NavigationGroup;
use App\Filament\Finance\Resources\RecurringInvoiceResource\Pages\CreateRecurringInvoice;
use App\Filament\Finance\Resources\RecurringInvoiceResource\Pages\EditRecurringInvoice;
use App\Filament\Finance\Resources\RecurringInvoiceResource\Pages\ListRecurringInvoices;
use App\Models\Finance\RecurringInvoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecurringInvoiceResource extends Resource
{
    protected static ?string $model = RecurringInvoice::class;

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Invoices->label();
    }

    public static function getModelLabel(): string
    {
        return __('finance.resources.recurring_invoices.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('finance.resources.recurring_invoices.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('finance.recurring-invoices.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('finance.recurring-invoices.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('finance.recurring-invoices.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('finance.recurring-invoices.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('finance.resources.recurring_invoices.sections.details'))
                ->schema([
                    Select::make('frequency')
                        ->options([
                            'weekly'    => 'Weekly',
                            'monthly'   => 'Monthly',
                            'quarterly' => 'Quarterly',
                            'annually'  => 'Annually',
                        ])
                        ->required(),

                    DatePicker::make('next_run_at')
                        ->label(__('finance.resources.recurring_invoices.fields.next_run_at'))
                        ->required()
                        ->native(false),

                    DatePicker::make('last_run_at')
                        ->label(__('finance.resources.recurring_invoices.fields.last_run_at'))
                        ->nullable()
                        ->native(false),

                    Toggle::make('is_active')
                        ->label(__('finance.resources.recurring_invoices.fields.is_active'))
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('frequency')
                    ->badge()
                    ->sortable(),

                TextColumn::make('next_run_at')
                    ->label(__('finance.resources.recurring_invoices.columns.next_run_at'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('last_run_at')
                    ->label(__('finance.resources.recurring_invoices.columns.last_run_at'))
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('finance.resources.recurring_invoices.columns.is_active'))
                    ->boolean(),
            ])
            ->defaultSort('next_run_at')
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

    public static function getPages(): array
    {
        return [
            'index'  => ListRecurringInvoices::route('/'),
            'create' => CreateRecurringInvoice::route('/create'),
            'edit'   => EditRecurringInvoice::route('/{record}/edit'),
        ];
    }
}
