<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\RoleResource\Pages\CreateRole;
use App\Filament\App\Resources\RoleResource\Pages\EditRole;
use App\Filament\App\Resources\RoleResource\Pages\ListRoles;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-shield-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationLabel(): string
    {
        return 'Roles';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Role Details')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->helperText('e.g. HR Manager, Finance Viewer'),
            ]),
            Section::make('Permissions')->schema([
                CheckboxList::make('permissions')
                    ->relationship('permissions', 'name')
                    ->searchable()
                    ->columns(3)
                    ->gridDirection('row'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions'),
                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                EditAction::make(),
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
