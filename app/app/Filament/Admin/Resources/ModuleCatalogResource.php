<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ModuleCatalogResource\Pages\CreateModuleCatalog;
use App\Filament\Admin\Resources\ModuleCatalogResource\Pages\EditModuleCatalog;
use App\Filament\Admin\Resources\ModuleCatalogResource\Pages\ListModuleCatalog;
use App\Models\ModuleCatalog;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ModuleCatalogResource extends Resource
{
    protected static ?string $model = ModuleCatalog::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-puzzle-piece';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Billing';
    }

    public static function getNavigationLabel(): string
    {
        return 'Module Catalog';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Module Details')->schema([
                TextInput::make('module_key')
                    ->required()
                    ->maxLength(100)
                    ->unique(ModuleCatalog::class, 'module_key', ignoreRecord: true)
                    ->helperText('e.g. hr.payroll, finance.invoicing'),
                TextInput::make('domain')
                    ->required()
                    ->maxLength(100)
                    ->helperText('e.g. hr, finance, crm'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Display name'),
                TextInput::make('per_user_monthly_price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0.00)
                    ->prefix('€')
                    ->label('Price per user/month'),
                Toggle::make('is_active')
                    ->label('Available for activation')
                    ->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module_key')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('domain')
                    ->sortable()
                    ->badge(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('per_user_monthly_price')
                    ->money('EUR')
                    ->sortable()
                    ->label('Price/user/month'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active modules only'),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListModuleCatalog::route('/'),
            'create' => CreateModuleCatalog::route('/create'),
            'edit'   => EditModuleCatalog::route('/{record}/edit'),
        ];
    }
}
