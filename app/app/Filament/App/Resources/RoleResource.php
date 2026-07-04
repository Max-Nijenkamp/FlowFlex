<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\DeleteRoleAction;
use App\Models\User;
use App\Services\BillingService;
use App\Support\Services\BuiltInRoles;
use App\Support\Services\CompanyContext;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

/**
 * Custom role management over spatie/laravel-permission (core.rbac):
 * permission matrix grouped per ACTIVE module, built-in roles protected.
 * Always-free platform module — permission gate only, no hasModule().
 */
class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Team';

    protected static ?string $navigationLabel = 'Roles';

    protected static ?string $modelLabel = 'role';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('core.rbac.view-any');
    }

    public static function getEloquentQuery(): Builder
    {
        // Roles are team-scoped by company_id, not CompanyScope'd — filter explicitly.
        return parent::getEloquentQuery()
            ->where('company_id', app(CompanyContext::class)->currentId());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Role')
                ->description('Members holding this role get the union of every checked permission.')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(64)
                        ->disabled(fn (?Role $record): bool => $record !== null && BuiltInRoles::isBuiltIn($record->name))
                        ->helperText(fn (?Role $record): ?string => $record !== null && BuiltInRoles::isBuiltIn($record->name)
                            ? 'Built-in role — the name is fixed.'
                            : null),
                ]),
            ...self::permissionMatrix(),
        ]);
    }

    /**
     * One section per ACTIVE module (module-scoped-permissions): permissions
     * of inactive modules can neither render nor be granted.
     *
     * @return array<int, Section>
     */
    protected static function permissionMatrix(): array
    {
        $companyId = app(CompanyContext::class)->currentId();

        if ($companyId === null) {
            return [];
        }

        $active = app(BillingService::class)->activeModules($companyId);

        $byModule = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->pluck('name')
            ->groupBy(fn (string $name): string => str($name)->beforeLast('.')->toString())
            ->only($active);

        return $byModule
            ->map(fn ($permissions, string $moduleKey): Section => Section::make($moduleKey)
                ->compact()
                ->schema([
                    CheckboxList::make('matrix.'.str_replace('.', '_', $moduleKey))
                        ->label('')
                        ->options($permissions->mapWithKeys(
                            fn (string $name): array => [$name => str($name)->afterLast('.')->headline()->toString()],
                        )->all())
                        ->columns(3)
                        ->bulkToggleable(),
                ]))
            ->values()
            ->all();
    }

    /** @param  array<string, mixed>  $matrix @return list<string> */
    public static function flattenMatrix(array $matrix): array
    {
        return collect($matrix)->flatten()->filter()->values()->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('users_count')
                    ->label('Members')
                    ->counts('users'),
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (Role $record): bool => BuiltInRoles::isBuiltIn($record->name))
                    ->using(function (Role $record): bool {
                        try {
                            DeleteRoleAction::run((string) $record->id);

                            return true;
                        } catch (Throwable $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();

                            return false;
                        }
                    }),
            ])
            ->emptyStateHeading('No roles yet')
            ->emptyStateDescription('Create a role to shape what your team can see and do.');
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
