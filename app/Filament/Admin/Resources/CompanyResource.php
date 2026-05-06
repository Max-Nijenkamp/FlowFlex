<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Country;
use App\Enums\Currency;
use App\Enums\Language;
use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\CompanyResource\Pages\CreateCompany;
use App\Filament\Admin\Resources\CompanyResource\Pages\EditCompany;
use App\Filament\Admin\Resources\CompanyResource\Pages\ListCompanies;
use App\Filament\Admin\Resources\CompanyResource\RelationManagers\ModulesRelationManager;
use App\Filament\Admin\Resources\CompanyResource\RelationManagers\TenantsRelationManager;
use App\Models\Company;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Platform;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Company Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, ?string $state, Set $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state ?? '')) : null
                        ),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->rules(['alpha_dash'])
                        ->helperText('Auto-generated from name. Lowercase, letters, numbers, hyphens only.'),

                    TextInput::make('email')
                        ->email()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(50),

                    TextInput::make('website')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://')
                        ->columnSpanFull(),
                ]),

            Section::make('Localisation')
                ->columns(3)
                ->schema([
                    Select::make('timezone')
                        ->options(fn () => collect(timezone_identifiers_list())
                            ->mapWithKeys(fn (string $tz) => [$tz => $tz])
                            ->toArray()
                        )
                        ->searchable()
                        ->default('Europe/Amsterdam')
                        ->required(),

                    Select::make('locale')
                        ->options(
                            collect(Language::cases())
                                ->mapWithKeys(fn (Language $l) => [$l->value => $l->flag() . ' ' . $l->nativeLabel()])
                                ->toArray()
                        )
                        ->default(Language::NL->value)
                        ->required(),

                    Select::make('currency')
                        ->options(
                            collect(Currency::cases())
                                ->mapWithKeys(fn (Currency $c) => [$c->value => $c->label()])
                                ->toArray()
                        )
                        ->default(Currency::EUR->value)
                        ->searchable()
                        ->required(),
                ]),

            Section::make('Addresses')
                ->schema([
                    Repeater::make('addresses')
                        ->relationship()
                        ->columns(2)
                        ->addActionLabel('Add address')
                        ->defaultItems(0)
                        ->schema([
                            Select::make('country')
                                ->options(
                                    collect(Country::cases())
                                        ->mapWithKeys(fn (Country $c) => [$c->value => $c->label()])
                                        ->toArray()
                                )
                                ->default(Country::NL->value)
                                ->required(),

                            Toggle::make('is_primary')
                                ->label('Primary address')
                                ->default(false)
                                ->inline(false),

                            TextInput::make('street')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('city')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('postal_code')
                                ->required()
                                ->maxLength(20),

                            TextInput::make('house_number')
                                ->required()
                                ->maxLength(20),

                            TextInput::make('house_number_addition')
                                ->label('Addition')
                                ->maxLength(20),
                        ]),
                ]),

            Section::make('Status')
                ->schema([
                    Toggle::make('is_enabled')
                        ->label('Company active')
                        ->helperText('Disabling a company blocks all of its users from logging in.')
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
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->fontFamily(FontFamily::Mono),

                TextColumn::make('email')
                    ->searchable()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('tenants_count')
                    ->label('Users')
                    ->counts('tenants')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_enabled')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->striped()
            ->filters([
                TernaryFilter::make('is_enabled')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->placeholder('All companies'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            TenantsRelationManager::class,
            ModulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit'   => EditCompany::route('/{record}/edit'),
        ];
    }
}
