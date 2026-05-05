<?php

namespace App\Filament\Admin\Resources\ModuleResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'subModules';

    protected static ?string $title = 'Sub-modules';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->width(40)
                    ->color('gray'),

                TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('key')
                    ->fontFamily(FontFamily::Mono)
                    ->color('gray')
                    ->size('sm'),

                TextColumn::make('description')
                    ->limit(80)
                    ->color('gray')
                    ->wrap(),

                IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->striped()
            ->paginated(false);
    }
}
