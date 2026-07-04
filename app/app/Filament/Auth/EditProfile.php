<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Profile page with independently saved sections (owner decision 2026-07-04):
 * no global save — each section validates and persists only its own fields.
 */
class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel(false)
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make('Profile')
                            ->description('Your name and work email.')
                            ->footerActions([
                                Action::make('saveProfile')
                                    ->label('Save profile')
                                    ->action('saveProfile'),
                            ])
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('First name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('last_name')
                                    ->label('Last name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Section::make('Password')
                            ->description('Pick something strong — the checklist fills in as you type.')
                            ->footerActions([
                                Action::make('savePassword')
                                    ->label('Update password')
                                    ->action('savePassword'),
                            ])
                            ->schema([
                                TextInput::make('current_password')
                                    ->label('Current password')
                                    ->password()
                                    ->revealable()
                                    ->autocomplete('current-password')
                                    ->required(),
                                TextInput::make('password')
                                    ->label('New password')
                                    ->id('ff-new-password')
                                    ->password()
                                    ->revealable()
                                    ->autocomplete('new-password')
                                    ->required()
                                    // Inside the field wrapper, not a schema row —
                                    // a collapsed schema row still costs a grid gap.
                                    ->belowContent(View::make('filament.chrome.password-checklist')),
                                TextInput::make('password_confirmation')
                                    ->label('Confirm new password')
                                    ->password()
                                    ->revealable()
                                    ->autocomplete('new-password')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    /** No global save/cancel — sections save independently. */
    protected function getFormActions(): array
    {
        return [];
    }

    public function saveProfile(): void
    {
        $user = $this->getUser();

        $validated = $this->validate(
            rules: [
                'data.first_name' => ['required', 'string', 'max:255'],
                'data.last_name' => ['required', 'string', 'max:255'],
                'data.email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique($user->getTable(), 'email')->ignore($user->getKey()),
                ],
            ],
            attributes: [
                'data.first_name' => 'first name',
                'data.last_name' => 'last name',
                'data.email' => 'email address',
            ],
        )['data'];

        $user->fill($validated)->save();

        Notification::make()
            ->success()
            ->title('Profile saved')
            ->send();
    }

    public function savePassword(): void
    {
        $user = $this->getUser();
        $guard = Filament::getAuthGuard();

        $validated = $this->validate(
            rules: [
                'data.current_password' => ['required', 'string', "current_password:{$guard}"],
                'data.password' => [
                    'required',
                    'string',
                    'confirmed',
                    Password::min(12)->letters()->mixedCase()->numbers()->symbols(),
                ],
                'data.password_confirmation' => ['required', 'string'],
            ],
            attributes: [
                'data.current_password' => 'current password',
                'data.password' => 'new password',
                'data.password_confirmation' => 'password confirmation',
            ],
        )['data'];

        // 'hashed' cast on both User and Admin — assign plain, never pre-hash.
        $user->update(['password' => $validated['password']]);

        // Keep the session alive: AuthenticateSession compares this hash.
        request()->session()->put("password_hash_{$guard}", $user->getAuthPassword());

        $this->data['current_password'] = null;
        $this->data['password'] = null;
        $this->data['password_confirmation'] = null;

        Notification::make()
            ->success()
            ->title('Password updated')
            ->send();
    }
}
