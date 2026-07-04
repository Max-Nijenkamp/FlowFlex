<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Actions\ProvisionCompanyAction;
use App\Data\ProvisionCompanyData;
use App\Filament\Admin\Resources\CompanyResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * Provisioning form (core.staff-console/company-provisioning): not a plain
 * CRUD create — fans out to roles, free modules and the owner invite via
 * ProvisionCompanyAction in one transaction.
 */
class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Workspace')
                ->description('The customer company being provisioned.')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                ]),
            Section::make('Owner')
                ->description('Receives the owner invitation to set up the workspace.')
                ->schema([
                    TextInput::make('owner_email')
                        ->label('Owner email')
                        ->email()
                        ->required(),
                ]),
            Section::make('Locale')
                ->description('Defaults the owner can change later in their settings.')
                ->columns(3)
                ->schema([
                    Select::make('timezone')
                        ->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))
                        ->default('Europe/Amsterdam')
                        ->searchable()
                        ->required(),
                    Select::make('locale')->options(['en' => 'English', 'nl' => 'Nederlands'])->default('en')->required(),
                    Select::make('currency')->options(['EUR' => 'EUR', 'USD' => 'USD', 'GBP' => 'GBP'])->default('EUR')->required(),
                ]),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return ProvisionCompanyAction::run(new ProvisionCompanyData(
            name: $data['name'],
            owner_email: $data['owner_email'],
            timezone: $data['timezone'],
            locale: $data['locale'],
            currency: $data['currency'],
        ));
    }
}
