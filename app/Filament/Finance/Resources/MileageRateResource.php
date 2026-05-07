<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Enums\NavigationGroup;
use App\Filament\Finance\Resources\MileageRateResource\Pages\CreateMileageRate;
use App\Filament\Finance\Resources\MileageRateResource\Pages\EditMileageRate;
use App\Filament\Finance\Resources\MileageRateResource\Pages\ListMileageRates;
use App\Models\Finance\MileageRate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MileageRateResource extends Resource
{
    protected static ?string $model = MileageRate::class;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Expenses->label();
    }

    public static function getModelLabel(): string
    {
        return __('finance.resources.mileage_rates.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('finance.resources.mileage_rates.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('finance.mileage-rates.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('finance.mileage-rates.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('finance.mileage-rates.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('finance.mileage-rates.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('finance.resources.mileage_rates.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('rate_per_km')
                        ->numeric()
                        ->required()
                        ->label(__('finance.resources.mileage_rates.fields.rate_per_km')),

                    Select::make('currency')
                        ->options([
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'USD' => 'USD',
                        ])
                        ->required(),

                    DatePicker::make('effective_from')
                        ->required()
                        ->native(false),

                    DatePicker::make('effective_to')
                        ->nullable()
                        ->native(false),

                    Toggle::make('is_active')
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
                    ->sortable(),

                TextColumn::make('rate_per_km')
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),

                TextColumn::make('currency'),

                TextColumn::make('effective_from')
                    ->date('d M Y'),

                TextColumn::make('effective_to')
                    ->date('d M Y')
                    ->placeholder('Ongoing'),

                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->defaultSort('name')
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
            'index'  => ListMileageRates::route('/'),
            'create' => CreateMileageRate::route('/create'),
            'edit'   => EditMileageRate::route('/{record}/edit'),
        ];
    }
}
