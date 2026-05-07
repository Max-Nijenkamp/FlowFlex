<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\PermissionResource\Pages\CreatePermission;
use App\Filament\Admin\Resources\PermissionResource\Pages\EditPermission;
use App\Filament\Admin\Resources\PermissionResource\Pages\ListPermissions;
use App\Models\Permission;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-key';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::AccessControl;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::AccessControl->label();
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.permissions.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.permissions.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.resources.permissions.sections.permission'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->helperText('Format: {module}.{resource}.{action} — e.g. hr.employees.view'),
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
                    ->weight(FontWeight::Medium)
                    ->fontFamily(FontFamily::Mono),

                TextColumn::make('roles_count')
                    ->label(__('admin.resources.permissions.columns.roles'))
                    ->counts('roles')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label(__('admin.resources.permissions.columns.created'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->striped()
            ->filters([
                SelectFilter::make('module')
                    ->label('Module')
                    ->options([
                        'platform'       => 'Platform',
                        'workspace'      => 'Workspace',
                        'hr'             => 'HR',
                        'projects'       => 'Projects',
                        'finance'        => 'Finance',
                        'crm'            => 'CRM',
                        'marketing'      => 'Marketing',
                        'operations'     => 'Operations',
                        'analytics'      => 'Analytics',
                        'it'             => 'IT',
                        'legal'          => 'Legal',
                        'ecommerce'      => 'Ecommerce',
                        'communications' => 'Communications',
                        'learning'       => 'Learning',
                    ])
                    ->query(fn ($query, $state) => $state['value']
                        ? $query->where('name', 'like', "{$state['value']}.%")
                        : $query
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
            'index'  => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'edit'   => EditPermission::route('/{record}/edit'),
        ];
    }
}
