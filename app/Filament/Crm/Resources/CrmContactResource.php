<?php

namespace App\Filament\Crm\Resources;

use App\Enums\Crm\ContactType;
use App\Filament\Crm\Enums\NavigationGroup;
use App\Filament\Crm\Resources\CrmContactResource\Pages\CreateCrmContact;
use App\Filament\Crm\Resources\CrmContactResource\Pages\EditCrmContact;
use App\Filament\Crm\Resources\CrmContactResource\Pages\ListCrmContacts;
use App\Filament\Crm\Resources\CrmContactResource\RelationManagers\CrmActivitiesRelationManager;
use App\Filament\Crm\Resources\CrmContactResource\RelationManagers\CrmContactCustomFieldsRelationManager;
use App\Models\Crm\CrmCompany;
use App\Models\Crm\CrmContact;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CrmContactResource extends Resource
{
    protected static ?string $model = CrmContact::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Contacts->label();
    }

    public static function getModelLabel(): string
    {
        return __('crm.resources.crm_contacts.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm.resources.crm_contacts.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('crm.contacts.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('crm.contacts.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('crm.contacts.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('crm.contacts.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('crm.resources.crm_contacts.sections.details'))
                ->schema([
                    TextInput::make('first_name')
                        ->label(__('crm.resources.crm_contacts.fields.first_name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('last_name')
                        ->label(__('crm.resources.crm_contacts.fields.last_name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label(__('crm.resources.crm_contacts.fields.email'))
                        ->email()
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label(__('crm.resources.crm_contacts.fields.phone'))
                        ->tel()
                        ->nullable()
                        ->maxLength(50),

                    TextInput::make('job_title')
                        ->label(__('crm.resources.crm_contacts.fields.job_title'))
                        ->nullable()
                        ->maxLength(255),

                    Select::make('type')
                        ->label(__('crm.resources.crm_contacts.fields.type'))
                        ->options(
                            collect(ContactType::cases())
                                ->mapWithKeys(fn (ContactType $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(ContactType::Lead->value)
                        ->required(),

                    Select::make('crm_company_id')
                        ->label(__('crm.resources.crm_contacts.fields.company'))
                        ->options(fn () => CrmCompany::query()->pluck('name', 'id')->toArray())
                        ->nullable()
                        ->searchable(),

                    TextInput::make('tags')
                        ->label(__('crm.resources.crm_contacts.fields.tags'))
                        ->nullable()
                        ->helperText('Comma-separated tags'),

                    TextInput::make('linkedin_url')
                        ->label(__('crm.resources.crm_contacts.fields.linkedin_url'))
                        ->url()
                        ->nullable()
                        ->maxLength(500),

                    Textarea::make('notes')
                        ->label(__('crm.resources.crm_contacts.fields.notes'))
                        ->nullable()
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('crm.resources.crm_contacts.columns.name'))
                    ->getStateUsing(fn (CrmContact $record) => $record->full_name)
                    ->searchable(query: function ($query, string $search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('email')
                    ->placeholder('—'),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (?ContactType $state) => $state?->label())
                    ->color(fn (?ContactType $state) => $state?->color()),

                TextColumn::make('crmCompany.name')
                    ->label(__('crm.resources.crm_contacts.columns.company'))
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label(__('crm.resources.crm_contacts.columns.added'))
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('crmCompany');
    }

    public static function getRelationManagers(): array
    {
        return [
            CrmContactCustomFieldsRelationManager::class,
            CrmActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCrmContacts::route('/'),
            'create' => CreateCrmContact::route('/create'),
            'edit'   => EditCrmContact::route('/{record}/edit'),
        ];
    }
}
