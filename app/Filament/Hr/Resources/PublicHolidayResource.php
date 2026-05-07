<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\PublicHolidayResource\Pages\CreatePublicHoliday;
use App\Filament\Hr\Resources\PublicHolidayResource\Pages\EditPublicHoliday;
use App\Filament\Hr\Resources\PublicHolidayResource\Pages\ListPublicHolidays;
use App\Models\Hr\PublicHoliday;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PublicHolidayResource extends Resource
{
    protected static ?string $model = PublicHoliday::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Leave;

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.leave-types.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.leave-types.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.leave-types.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.leave-types.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Public Holiday Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('country_code')
                        ->label('Country Code')
                        ->helperText('ISO 3166-1 alpha-2, e.g. GB, NL, US')
                        ->required()
                        ->maxLength(2)
                        ->minLength(2)
                        ->dehydrateStateUsing(fn (?string $state) => $state ? strtoupper($state) : $state),

                    DatePicker::make('date')
                        ->required()
                        ->native(false),

                    Toggle::make('is_recurring')
                        ->label('Recurring Annually')
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

                TextColumn::make('country_code')
                    ->label('Country')
                    ->badge(),

                TextColumn::make('date')
                    ->date('d M Y')
                    ->sortable(),

                IconColumn::make('is_recurring')
                    ->label('Recurring')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'asc')
            ->striped()
            ->filters([
                SelectFilter::make('country_code')
                    ->label('Country')
                    ->options(fn () => PublicHoliday::query()
                        ->distinct()
                        ->pluck('country_code', 'country_code')
                        ->toArray()
                    ),
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

    public static function getPages(): array
    {
        return [
            'index'  => ListPublicHolidays::route('/'),
            'create' => CreatePublicHoliday::route('/create'),
            'edit'   => EditPublicHoliday::route('/{record}/edit'),
        ];
    }
}
