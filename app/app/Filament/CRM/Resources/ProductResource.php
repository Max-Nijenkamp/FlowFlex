<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Product;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|UnitEnum|null $navigationGroup = 'Pricing';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.pricing.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.pricing');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Product')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(120),
                    TextInput::make('sku')->label('SKU')->maxLength(60),
                    TextInput::make('unit')->placeholder('piece, hour, seat…'),
                    TextInput::make('standard_price_cents')->label('Price (€)')->numeric()->required()
                        ->formatStateUsing(fn ($state): float => $state === null ? 0.0 : round($state / 100, 2))
                        ->dehydrateStateUsing(fn ($state): int => (int) round((float) $state * 100)),
                    TextInput::make('cost_cents')->label('Cost (€)')->numeric()
                        ->formatStateUsing(fn ($state): float => $state === null ? 0.0 : round($state / 100, 2))
                        ->dehydrateStateUsing(fn ($state): int => (int) round((float) $state * 100)),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                    Textarea::make('description')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('sku')->searchable(),
                TextColumn::make('standard_price_cents')->label('Price')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100, 2)),
                TextColumn::make('unit'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ProductResource\Pages\ListProducts::route('/'),
        ];
    }
}
