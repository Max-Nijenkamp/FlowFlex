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

    protected static ?int $navigationSort = 0;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Payroll->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.payroll_entities.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.payroll_entities.plural');
    }

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
            Section::make(__('hr.resources.payroll_entities.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('hr.resources.payroll_entities.fields.display_name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('legal_name')
                        ->label(__('hr.resources.payroll_entities.fields.legal_name'))
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('country_code')
                        ->label(__('hr.resources.payroll_entities.fields.country_code'))
                        ->helperText('ISO 3166-1 alpha-2, e.g. GB, NL, US')
                        ->required()
                        ->maxLength(2)
                        ->minLength(2)
                        ->dehydrateStateUsing(fn (?string $state) => $state ? strtoupper($state) : $state),

                    TextInput::make('tax_reference_encrypted')
                        ->label(__('hr.resources.payroll_entities.fields.tax_reference'))
                        ->nullable()
                        ->maxLength(255)
                        ->password()
                        ->revealable(),

                    Toggle::make('is_default')
                        ->label(__('hr.resources.payroll_entities.fields.default_entity'))
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
                    ->label(__('hr.resources.payroll_entities.columns.legal_name'))
                    ->placeholder('—'),

                TextColumn::make('country_code')
                    ->label(__('hr.resources.payroll_entities.columns.country'))
                    ->badge(),

                TextColumn::make('tax_reference_encrypted')
                    ->label(__('hr.resources.payroll_entities.columns.tax_reference'))
                    ->formatStateUsing(fn ($state) => $state ? '••••••••' : '—')
                    ->placeholder('—'),

                IconColumn::make('is_default')
                    ->label(__('hr.resources.payroll_entities.columns.is_default'))
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
