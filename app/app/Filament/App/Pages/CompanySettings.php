<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Data\Foundation\UpdateCompanyData;
use App\Models\Company;
use App\Services\Foundation\CompanyService;
use App\Support\Services\CompanyContext;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class CompanySettings extends Page
{
    public ?array $data = [];

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationLabel(): string
    {
        return 'Company Settings';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function canAccess(): bool
    {
        return auth()->check()
            && auth()->user()->checkPermissionTo('core.company.settings.manage');
    }

    public function getView(): string
    {
        return 'filament.app.pages.company-settings';
    }

    public function mount(): void
    {
        $company = app(CompanyContext::class)->current();

        $this->form->fill([
            'name'          => $company->name,
            'slug'          => $company->slug,
            'email'         => $company->email,
            'timezone'      => $company->timezone,
            'locale'        => $company->locale,
            'currency'      => $company->currency,
            'logo_path'     => $company->logo_path ?? null,
            'favicon_path'  => $company->favicon_path ?? null,
            'primary_color' => $company->primary_color ?? null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Company Information')->components([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label('Company name'),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(100)
                        ->label('Slug')
                        ->unique(
                            table: Company::class,
                            column: 'slug',
                            ignorable: fn () => app(CompanyContext::class)->current(),
                        ),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->label('Billing email'),
                ])->columns(2),
                \Filament\Schemas\Components\Section::make('Localisation')->components([
                    Select::make('timezone')
                        ->options(fn () => array_combine(
                            \DateTimeZone::listIdentifiers(),
                            \DateTimeZone::listIdentifiers(),
                        ))
                        ->searchable()
                        ->required(),
                    Select::make('locale')
                        ->options([
                            'en'    => 'English',
                            'nl'    => 'Dutch',
                            'de'    => 'German',
                            'fr'    => 'French',
                            'es'    => 'Spanish',
                            'en-GB' => 'English (UK)',
                            'nl-NL' => 'Dutch (Netherlands)',
                        ])
                        ->required(),
                    Select::make('currency')
                        ->options([
                            'EUR' => 'Euro (EUR)',
                            'USD' => 'US Dollar (USD)',
                            'GBP' => 'British Pound (GBP)',
                            'CHF' => 'Swiss Franc (CHF)',
                        ])
                        ->required(),
                ])->columns(3),
                \Filament\Schemas\Components\Section::make('Branding')->components([
                    FileUpload::make('logo_path')
                        ->label('Logo')
                        ->image()
                        ->directory('company-assets')
                        ->visibility('public'),
                    FileUpload::make('favicon_path')
                        ->label('Favicon')
                        ->image()
                        ->directory('company-assets')
                        ->visibility('public'),
                    ColorPicker::make('primary_color')
                        ->label('Primary colour'),
                ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        abort_unless(static::canAccess(), 403);

        $data = $this->form->getState();
        $company = app(CompanyContext::class)->current();

        $updateData = new UpdateCompanyData(
            name:     $data['name'],
            slug:     $data['slug'],
            email:    $data['email'],
            timezone: $data['timezone'],
            locale:   $data['locale'],
            currency: $data['currency'],
        );

        // Persist branding fields directly (logo_path, favicon_path, primary_color
        // require migration 010011_add_branding_to_companies.php)
        $company = app(CompanyContext::class)->current();
        $company->fill([
            'logo_path'     => $data['logo_path'] ?? null,
            'favicon_path'  => $data['favicon_path'] ?? null,
            'primary_color' => $data['primary_color'] ?? null,
        ])->save();

        app(CompanyService::class)->update($company->id, $updateData);

        Notification::make()
            ->title('Company settings saved')
            ->success()
            ->send();
    }
}
