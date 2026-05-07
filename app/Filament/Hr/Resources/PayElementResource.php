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

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Payroll;

    protected static ?int $navigationSort = 1;

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
            Section::make('Pay Element Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('code')
                        ->required()
                        ->maxLength(20),

                    Select::make('type')
                        ->options(
                            collect(PayElementType::cases())
                                ->mapWithKeys(fn (PayElementType $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Active')
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

                TextColumn::make('code')
                    ->badge(),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (?PayElementType $state) => $state?->label()),

                IconColumn::make('is_active')
                    ->label('Active')
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
