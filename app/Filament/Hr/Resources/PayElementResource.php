<?php

namespace App\Filament\Hr\Resources;

use App\Enums\Hr\PayElementType;
use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\PayElementResource\Pages\CreatePayElement;
use App\Filament\Hr\Resources\PayElementResource\Pages\EditPayElement;
use App\Filament\Hr\Resources\PayElementResource\Pages\ListPayElements;
use App\Models\Hr\PayElement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayElementResource extends Resource
{
    protected static ?string $model = PayElement::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Payroll->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.pay_elements.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.pay_elements.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.pay-elements.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.pay-elements.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.pay-elements.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.pay-elements.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('hr.resources.pay_elements.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Select::make('element_type')
                        ->label(__('hr.resources.pay_elements.fields.element_type'))
                        ->options(
                            collect(PayElementType::cases())
                                ->mapWithKeys(fn (PayElementType $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default('basic_salary')
                        ->required(),

                    Toggle::make('is_taxable')
                        ->label(__('hr.resources.pay_elements.fields.is_taxable'))
                        ->default(true),

                    Toggle::make('is_pensionable')
                        ->label(__('hr.resources.pay_elements.fields.is_pensionable'))
                        ->default(false),

                    Toggle::make('is_active')
                        ->label(__('hr.resources.pay_elements.fields.is_active'))
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('element_type')
                    ->label(__('hr.resources.pay_elements.columns.type'))
                    ->badge()
                    ->formatStateUsing(fn (?PayElementType $state) => $state?->label()),

                IconColumn::make('is_taxable')
                    ->label(__('hr.resources.pay_elements.columns.is_taxable'))
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label(__('hr.resources.pay_elements.columns.is_active'))
                    ->boolean(),
            ])
            ->striped()
            ->actions([
                EditAction::make(),
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
            'index'  => ListPayElements::route('/'),
            'create' => CreatePayElement::route('/create'),
            'edit'   => EditPayElement::route('/{record}/edit'),
        ];
    }
}
