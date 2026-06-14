<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\Finance\FixedAsset;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class FixedAssetResource extends Resource
{
    protected static ?string $model = FixedAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|UnitEnum|null $navigationGroup = 'Assets';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.assets.view-any')
            && app(BillingServiceInterface::class)->hasModule('finance.assets');
    }

    public static function canCreate(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.assets.create');
    }

    public static function form(Schema $schema): Schema
    {
        // Financial fields are immutable after capitalisation — the acquisition
        // journal (FixedAssetService::create) is posted from these values.
        $immutableOnEdit = fn (string $operation): bool => $operation === 'edit';

        return $schema->components([
            Section::make('Fixed asset')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(120),
                    TextInput::make('category')->required()->maxLength(60),
                    TextInput::make('cost_cents')->label('Cost (cents)')
                        ->numeric()->integer()->minValue(1)->required()
                        ->disabled($immutableOnEdit),
                    DatePicker::make('purchase_date')->required()->default(now())
                        ->disabled($immutableOnEdit),
                    TextInput::make('useful_life_months')->label('Useful life (months)')
                        ->numeric()->integer()->minValue(1)->required()
                        ->disabled($immutableOnEdit),
                    Select::make('method')
                        ->options([
                            'straight-line' => 'Straight line',
                            'declining-balance' => 'Declining balance',
                        ])
                        ->default('straight-line')
                        ->required()
                        ->disabled($immutableOnEdit),
                    TextInput::make('salvage_cents')->label('Salvage value (cents)')
                        ->numeric()->integer()->minValue(0)->default(0)->required()
                        ->disabled($immutableOnEdit),
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
                TextColumn::make('category'),
                TextColumn::make('cost_cents')->label('Cost')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100, 2)),
                TextColumn::make('accumulated_depreciation_cents')->label('Depreciated')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100, 2)),
                TextColumn::make('status')->badge(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => Auth::guard('web')->user()->can('finance.assets.update')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => FixedAssetResource\Pages\ListFixedAssets::route('/'),
        ];
    }
}
