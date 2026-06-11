<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\DeleteRoleAction;
use App\Contracts\BillingServiceInterface;
use App\Exceptions\CannotDeleteBuiltInRoleException;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Team';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.rbac.view-any')
            && app(BillingServiceInterface::class)->hasModule('core.rbac');
    }

    public static function getEloquentQuery(): Builder
    {
        // Spatie roles are team-scoped via team_id = company_id.
        return parent::getEloquentQuery()->where('team_id', getPermissionsTeamId());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(100),
            CheckboxList::make('permissions')
                ->relationship('permissions', 'name')
                ->options(
                    Permission::query()->where('guard_name', 'web')->orderBy('name')->pluck('name', 'id'),
                )
                ->columns(2)
                ->searchable()
                ->bulkToggleable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('permissions_count')->counts('permissions')->label('Permissions'),
                TextColumn::make('users_count')->counts('users')->label('Users'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->using(function (Role $record): void {
                        try {
                            DeleteRoleAction::run($record->id);
                        } catch (CannotDeleteBuiltInRoleException|\RuntimeException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => RoleResource\Pages\ListRoles::route('/'),
            'create' => RoleResource\Pages\CreateRole::route('/create'),
            'edit' => RoleResource\Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
