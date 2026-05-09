<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdminUserResource\Pages\CreateAdminUser;
use App\Filament\Admin\Resources\AdminUserResource\Pages\EditAdminUser;
use App\Filament\Admin\Resources\AdminUserResource\Pages\ListAdminUsers;
use App\Models\Admin;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminUserResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $modelLabel = 'Admin User';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Team';
    }

    public static function getNavigationLabel(): string
    {
        return 'Admin Users';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Admin Details')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(Admin::class, 'email', ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $context) => $context === 'create')
                    ->minLength(12)
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'support'     => 'Support',
                        'billing'     => 'Billing',
                        'developer'   => 'Developer',
                    ])
                    ->required()
                    ->default('support'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'support'     => 'warning',
                        'billing'     => 'info',
                        'developer'   => 'success',
                        default       => 'gray',
                    }),
                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last login'),
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
            'index'  => ListAdminUsers::route('/'),
            'create' => CreateAdminUser::route('/create'),
            'edit'   => EditAdminUser::route('/{record}/edit'),
        ];
    }
}
