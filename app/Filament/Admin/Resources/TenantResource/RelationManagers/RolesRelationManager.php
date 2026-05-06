<?php

namespace App\Filament\Admin\Resources\TenantResource\RelationManagers;

use App\Models\Role;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $title = 'Roles';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight(FontWeight::Medium)
                    ->badge()
                    ->color('primary'),

                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('gray'),
            ])
            ->striped()
            ->headerActions([
                Action::make('assign')
                    ->label('Assign Role')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Select::make('role_id')
                            ->label('Role')
                            ->options(
                                Role::where('guard_name', 'tenant')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data) {
                        $role = Role::find($data['role_id']);

                        if (! $role) {
                            return;
                        }

                        $tenant = $this->getOwnerRecord();

                        if ($tenant->hasRole($role)) {
                            Notification::make()
                                ->warning()
                                ->title("User already has role \"{$role->name}\"")
                                ->send();
                            return;
                        }

                        $tenant->assignRole($role);

                        Notification::make()
                            ->success()
                            ->title("Role \"{$role->name}\" assigned")
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Role $record) {
                        $this->getOwnerRecord()->removeRole($record);

                        Notification::make()
                            ->success()
                            ->title("Role \"{$record->name}\" revoked")
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }
}
