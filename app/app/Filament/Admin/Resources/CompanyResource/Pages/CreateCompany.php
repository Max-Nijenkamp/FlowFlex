<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Actions\ProvisionCompanyAction;
use App\Data\ProvisionCompanyData;
use App\Filament\Admin\Resources\CompanyResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * Staff onboarding flow — not a bare Company insert: provisions owner role,
 * free core modules and the owner invitation in one transaction.
 */
class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Company')
                ->description('Provisions the workspace, owner role, free core modules and the owner invitation in one go.')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(120),
                    TextInput::make('owner_email')->label('Owner email')->email()->required()
                        ->helperText('Receives the owner invitation — they complete setup themselves.'),
                ]),
            Section::make('Localisation')
                ->columns(3)
                ->components([
                    TextInput::make('timezone')->required()->default('Europe/Amsterdam'),
                    TextInput::make('locale')->required()->default('nl')->maxLength(10),
                    TextInput::make('currency')->required()->default('EUR')->length(3),
                ]),
        ]);
    }

    /** @param array<string, mixed> $data */
    protected function handleRecordCreation(array $data): Model
    {
        return ProvisionCompanyAction::run(ProvisionCompanyData::from($data));
    }
}
