<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\AssignRolesAction;
use App\Contracts\BillingServiceInterface;
use App\Data\AssignRolesData;
use App\Exceptions\CannotRemoveLastOwnerException;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Team';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.rbac.view-any')
            && app(BillingServiceInterface::class)->hasModule('core.rbac');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->state(fn (User $record) => $record->full_name)
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')->badge()->label('Roles'),
                IconColumn::make('email_deliverable')->boolean()->label('Deliverable'),
                TextColumn::make('last_login_at')->dateTime()->sortable()->placeholder('Never'),
            ])
            ->recordActions([
                Action::make('roles')
                    ->label('Roles')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->visible(fn () => Auth::guard('web')->user()->can('core.rbac.assign'))
                    ->schema([
                        CheckboxList::make('roles')
                            ->options(fn () => Role::query()
                                ->where('team_id', getPermissionsTeamId())
                                ->pluck('name', 'name'))
                            ->default(fn (User $record) => $record->roles->pluck('name')->all()),
                    ])
                    ->action(function (User $record, array $data): void {
                        try {
                            AssignRolesAction::run(new AssignRolesData(
                                user_id: $record->id,
                                roles: $data['roles'] ?? [],
                            ));
                            Notification::make()->success()->title('Roles updated')->send();
                        } catch (CannotRemoveLastOwnerException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => UserResource\Pages\ListUsers::route('/'),
        ];
    }
}
