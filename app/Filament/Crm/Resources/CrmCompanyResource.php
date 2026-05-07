<?php

namespace App\Filament\Crm\Resources;

use App\Filament\Crm\Enums\NavigationGroup;
use App\Filament\Crm\Resources\CrmCompanyResource\Pages\CreateCrmCompany;
use App\Filament\Crm\Resources\CrmCompanyResource\Pages\EditCrmCompany;
use App\Filament\Crm\Resources\CrmCompanyResource\Pages\ListCrmCompanies;
use App\Filament\Crm\Resources\CrmCompanyResource\RelationManagers\CrmActivitiesRelationManager;
use App\Models\Crm\CrmCompany;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CrmCompanyResource extends Resource
{
    protected static ?string $model = CrmCompany::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Contacts->label();
    }

    public static function getModelLabel(): string
    {
        return __('crm.resources.crm_companies.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm.resources.crm_companies.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('crm.companies.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('crm.companies.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('crm.companies.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('crm.companies.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('crm.resources.crm_companies.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('website')
                        ->url()
                        ->nullable()
                        ->maxLength(500),

                    TextInput::make('phone')
                        ->label(__('crm.resources.crm_companies.fields.phone'))
                        ->tel()
                        ->nullable()
                        ->maxLength(50),

                    TextInput::make('industry')
                        ->label(__('crm.resources.crm_companies.fields.industry'))
                        ->nullable()
                        ->maxLength(255),

                    Textarea::make('notes')
                        ->label(__('crm.resources.crm_companies.fields.notes'))
                        ->nullable()
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('website')
                    ->placeholder('—'),

                TextColumn::make('industry')
                    ->placeholder('—'),

                TextColumn::make('contacts_count')
                    ->label(__('crm.resources.crm_companies.columns.contacts'))
                    ->counts('contacts'),

                TextColumn::make('deals_count')
                    ->label(__('crm.resources.crm_companies.columns.deals'))
                    ->counts('deals'),
            ])
            ->defaultSort('name')
            ->striped()
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            CrmActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCrmCompanies::route('/'),
            'create' => CreateCrmCompany::route('/create'),
            'edit'   => EditCrmCompany::route('/{record}/edit'),
        ];
    }
}
