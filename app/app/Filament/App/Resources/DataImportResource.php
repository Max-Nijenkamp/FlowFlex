<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\DataImport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Import history. The upload + mapping wizard lands with the first registered
 * importer (hr.profiles / crm.contacts) — until then the registry is empty
 * and there is nothing to import into.
 */
class DataImportResource extends Resource
{
    protected static ?string $model = DataImport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $modelLabel = 'data import';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.import.view-any')
            && app(BillingServiceInterface::class)->hasModule('core.import');
    }

    public static function canCreate(): bool
    {
        return false; // wizard arrives with the first importer target
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('target')->badge(),
                TextColumn::make('filename'),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'complete' => 'success',
                        'failed' => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('success_rows')->label('OK'),
                TextColumn::make('error_rows')->label('Errors'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DataImportResource\Pages\ListDataImports::route('/'),
        ];
    }
}
