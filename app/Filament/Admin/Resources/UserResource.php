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

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Platform->label();
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.users.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.users.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.resources.users.sections.account_information'))
                ->columns(2)
                ->footerActions([
                    Action::make('saveAccountInfo')
                        ->label(__('admin.resources.users.actions.save_changes'))
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

            Section::make(__('admin.resources.users.sections.password'))
                ->hidden(fn (string $operation): bool => $operation === 'edit')
                ->schema([
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make(__('admin.resources.users.sections.change_password'))
                ->hidden(fn (string $operation): bool => $operation === 'create')
                ->footerActions([
                    Action::make('updatePassword')
                        ->label(__('admin.resources.users.actions.update_password'))
                        ->icon('heroicon-o-key')
                        ->action(function ($livewire): void {
                            $current  = $livewire->data['current_password'] ?? null;
                            $new      = $livewire->data['new_password'] ?? null;
                            $confirm  = $livewire->data['new_password_confirmation'] ?? null;

                            if (! Hash::check($current, $livewire->getRecord()->getAuthPassword())) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('admin.resources.users.notifications.password_incorrect'))
                                    ->send();

                                return;
                            }

                            if ($new !== $confirm) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('admin.resources.users.notifications.passwords_mismatch'))
                                    ->send();

                                return;
                            }

                            if (strlen($new) < 8) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('admin.resources.users.notifications.password_too_short'))
                                    ->send();

                                return;
                            }

                            $livewire->getRecord()->update(['password' => $new]);

                            $livewire->data['current_password']         = null;
                            $livewire->data['new_password']             = null;
                            $livewire->data['new_password_confirmation'] = null;

                            Notification::make()
                                ->success()
                                ->title(__('admin.resources.users.notifications.password_updated'))
                                ->send();
                        }),
                ])
                ->columns(1)
                ->schema([
                    TextInput::make('current_password')
                        ->label(__('admin.resources.users.fields.current_password'))
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->required(),

                    TextInput::make('new_password')
                        ->label(__('admin.resources.users.fields.new_password'))
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->required()
                        ->minLength(8),

                    TextInput::make('new_password_confirmation')
                        ->label(__('admin.resources.users.fields.confirm_new_password'))
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
                    ->label(__('admin.resources.users.columns.joined'))
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
