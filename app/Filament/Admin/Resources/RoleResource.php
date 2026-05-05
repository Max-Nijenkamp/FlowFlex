<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\RoleResource\Pages\CreateRole;
use App\Filament\Admin\Resources\RoleResource\Pages\EditRole;
use App\Filament\Admin\Resources\RoleResource\Pages\ListRoles;
use App\Models\Permission;
use App\Models\Role;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::AccessControl;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        $modules = [
            'Platform'       => 'platform',
            'Workspace'      => 'workspace',
            'HR'             => 'hr',
            'Projects'       => 'projects',
            'Finance'        => 'finance',
            'CRM'            => 'crm',
            'Marketing'      => 'marketing',
            'Operations'     => 'operations',
            'Analytics'      => 'analytics',
            'IT'             => 'it',
            'Legal'          => 'legal',
            'Ecommerce'      => 'ecommerce',
            'Communications' => 'communications',
            'Learning'       => 'learning',
        ];

        $tabs = [];

        foreach ($modules as $label => $prefix) {
            $options = Permission::where('guard_name', 'tenant')
                ->where('name', 'like', "{$prefix}.%")
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();

            if (empty($options)) {
                continue;
            }

            $tabs[] = Tab::make($label)
                ->schema([
                    CheckboxList::make("permissions_{$prefix}")
                        ->label('')
                        ->options($options)
                        ->bulkToggleable()
                        ->columns(3)
                        ->gridDirection('row')
                        ->dehydrated(false),
                ]);
        }

        return $schema->components([
            Section::make('Role')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->helperText('Use kebab-case: hr-manager, workspace-admin'),
                ]),

            Section::make('Permissions')
                ->schema([
                    Tabs::make('PermissionTabs')
                        ->tabs($tabs)
                        ->contained(false),
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

                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
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
            'index'  => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit'   => EditRole::route('/{record}/edit'),
        ];
    }
}
