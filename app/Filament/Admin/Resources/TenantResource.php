<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\TenantResource\Pages\CreateTenant;
use App\Filament\Admin\Resources\TenantResource\Pages\EditTenant;
use App\Filament\Admin\Resources\TenantResource\Pages\ListTenants;
use App\Filament\Admin\Resources\TenantResource\RelationManagers\RolesRelationManager;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Platform;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Platform->label();
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.tenants.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.tenants.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.resources.tenants.sections.workspace'))
                ->schema([
                    Select::make('company_id')
                        ->label(__('admin.resources.tenants.fields.company'))
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make(__('admin.resources.tenants.sections.name'))
                ->columns(3)
                ->schema([
                    TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('middle_name')
                        ->maxLength(255),

                    TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make(__('admin.resources.tenants.sections.contact'))
                ->columns(2)
                ->schema([
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(50),
                ]),

            Section::make(__('admin.resources.tenants.sections.password'))
                ->schema([
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->helperText(fn (string $operation) => $operation === 'edit'
                            ? 'Leave blank to keep the current password.'
                            : null
                        )
                        ->maxLength(255),
                ]),

            Section::make(__('admin.resources.tenants.sections.status'))
                ->schema([
                    Toggle::make('is_enabled')
                        ->label(__('admin.resources.tenants.fields.user_active'))
                        ->helperText('Inactive users cannot log in to any panel.')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('admin.resources.tenants.columns.name'))
                    ->getStateUsing(fn (Tenant $record) => $record->fullName())
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['last_name'])
                    ->weight(FontWeight::Medium),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('company.name')
                    ->label(__('admin.resources.tenants.columns.company'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_enabled')
                    ->label(__('admin.resources.tenants.columns.active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('admin.resources.tenants.columns.joined'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('last_name')
            ->striped()
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('admin.resources.tenants.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_enabled')
                    ->label(__('admin.resources.tenants.filters.status'))
                    ->boolean()
                    ->trueLabel(__('admin.resources.tenants.filters.active_only'))
                    ->falseLabel(__('admin.resources.tenants.filters.inactive_only'))
                    ->placeholder(__('admin.resources.tenants.filters.all_users')),
            ])
            ->actions([
                Action::make('impersonate')
                    ->label(__('admin.resources.tenants.actions.impersonate'))
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Tenant $record) => "Impersonate {$record->fullName()}?")
                    ->modalDescription('You will be logged in as this user in the workspace panel.')
                    ->action(function (Tenant $record) {
                        session([
                            'impersonating_user_id'   => Auth::id(),
                            'impersonating_user_name' => Auth::user()->name,
                        ]);

                        Auth::guard('tenant')->login($record);

                        return redirect('/workspace');
                    }),

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
            RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'edit'   => EditTenant::route('/{record}/edit'),
        ];
    }
}
