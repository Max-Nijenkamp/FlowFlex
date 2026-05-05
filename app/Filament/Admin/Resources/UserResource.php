<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\UserResource\Pages\CreateUser;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use App\Filament\Admin\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Platform;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account Information')
                ->columns(2)
                ->footerActions([
                    Action::make('saveAccountInfo')
                        ->label('Save changes')
                        ->icon('heroicon-o-check')
                        ->visible(fn (string $operation): bool => $operation === 'edit')
                        ->action(fn ($livewire, $component) => $livewire->saveFormComponentOnly($component)),
                ])
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                ]),

            Section::make('Password')
                ->hidden(fn (string $operation): bool => $operation === 'edit')
                ->schema([
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make('Change Password')
                ->hidden(fn (string $operation): bool => $operation === 'create')
                ->footerActions([
                    Action::make('updatePassword')
                        ->label('Update password')
                        ->icon('heroicon-o-key')
                        ->action(function ($livewire): void {
                            $current  = $livewire->data['current_password'] ?? null;
                            $new      = $livewire->data['new_password'] ?? null;
                            $confirm  = $livewire->data['new_password_confirmation'] ?? null;

                            if (! Hash::check($current, $livewire->getRecord()->getAuthPassword())) {
                                Notification::make()
                                    ->danger()
                                    ->title('Current password is incorrect')
                                    ->send();

                                return;
                            }

                            if ($new !== $confirm) {
                                Notification::make()
                                    ->danger()
                                    ->title('New passwords do not match')
                                    ->send();

                                return;
                            }

                            if (strlen($new) < 8) {
                                Notification::make()
                                    ->danger()
                                    ->title('Password must be at least 8 characters')
                                    ->send();

                                return;
                            }

                            $livewire->getRecord()->update(['password' => $new]);

                            $livewire->data['current_password']         = null;
                            $livewire->data['new_password']             = null;
                            $livewire->data['new_password_confirmation'] = null;

                            Notification::make()
                                ->success()
                                ->title('Password updated')
                                ->send();
                        }),
                ])
                ->columns(1)
                ->schema([
                    TextInput::make('current_password')
                        ->label('Current password')
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->required(),

                    TextInput::make('new_password')
                        ->label('New password')
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->required()
                        ->minLength(8),

                    TextInput::make('new_password_confirmation')
                        ->label('Confirm new password')
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->required()
                        ->same('new_password'),
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

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Joined')
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
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}
