<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Tenant profile page. Users carry first_name/last_name — Filament's default
 * profile edits a single `name` attribute that doesn't exist on User, so
 * saving silently changed nothing (founder report 2026-06-12).
 */
class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('first_name')
                ->label('First name')
                ->required()
                ->maxLength(120),
            TextInput::make('last_name')
                ->label('Last name')
                ->required()
                ->maxLength(120),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            $this->getCurrentPasswordFormComponent(),
        ]);
    }
}
