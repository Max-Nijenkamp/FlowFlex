<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\PayrollEntityResource\Pages\CreatePayrollEntity;
use App\Filament\Hr\Resources\PayrollEntityResource\Pages\EditPayrollEntity;
use App\Filament\Hr\Resources\PayrollEntityResource\Pages\ListPayrollEntities;
use App\Models\Hr\PayrollEntity;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollEntityResource extends Resource
{
    protected static ?string $model = PayrollEntity::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Payroll;

    protected static ?int $navigationSort = 0;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.payroll.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.payroll.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.payroll.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.payroll.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payroll Entity Details')
                ->schema([
                    TextInput::make('name')
                        ->label('Display Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('legal_name')
                        ->label('Legal Name')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('country_code')
                        ->label('Country Code')
                        ->helperText('ISO 3166-1 alpha-2, e.g. GB, NL, US')
                        ->required()
                        ->maxLength(2)
                        ->minLength(2)
                        ->dehydrateStateUsing(fn (?string $state) => $state ? strtoupper($state) : $state),

                    TextInput::make('tax_reference')
                        ->label('Tax Reference')
                        ->nullable()
                        ->maxLength(255),

                    Toggle::make('is_default')
                        ->label('Default Entity')
                        ->default(false),
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

                TextColumn::make('legal_name')
                    ->label('Legal Name')
                    ->placeholder('—'),

                TextColumn::make('country_code')
                    ->label('Country')
                    ->badge(),

                TextColumn::make('tax_reference')
                    ->label('Tax Reference')
                    ->placeholder('—'),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index'  => ListPayrollEntities::route('/'),
            'create' => CreatePayrollEntity::route('/create'),
            'edit'   => EditPayrollEntity::route('/{record}/edit'),
        ];
    }
}
