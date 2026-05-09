<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompanyFeatureFlagResource\Pages\CreateCompanyFeatureFlag;
use App\Filament\Admin\Resources\CompanyFeatureFlagResource\Pages\EditCompanyFeatureFlag;
use App\Filament\Admin\Resources\CompanyFeatureFlagResource\Pages\ListCompanyFeatureFlags;
use App\Models\Company;
use App\Models\CompanyFeatureFlag;
use Filament\Forms\Components\Select;
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

class CompanyFeatureFlagResource extends Resource
{
    protected static ?string $model = CompanyFeatureFlag::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-flag';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationLabel(): string
    {
        return 'Feature Flags';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Feature Flag Details')->schema([
                Select::make('company_id')
                    ->label('Company (leave blank for global flag)')
                    ->options(fn () => Company::withoutGlobalScopes()->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->placeholder('Global flag'),
                TextInput::make('flag')
                    ->required()
                    ->maxLength(100)
                    ->helperText('e.g. beta.ai-copilot, feature.new-dashboard'),
                Toggle::make('enabled')
                    ->label('Enabled')
                    ->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(CompanyFeatureFlag::withoutGlobalScopes())
            ->columns([
                TextColumn::make('company.name')
                    ->label('Company')
                    ->default('Global')
                    ->sortable(),
                TextColumn::make('flag')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('enabled')
                    ->boolean()
                    ->label('Enabled'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('enabled')
                    ->label('Enabled flags only'),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCompanyFeatureFlags::route('/'),
            'create' => CreateCompanyFeatureFlag::route('/create'),
            'edit'   => EditCompanyFeatureFlag::route('/{record}/edit'),
        ];
    }
}
