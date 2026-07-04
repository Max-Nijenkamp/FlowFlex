<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\User;
use App\Services\BillingService;
use App\Settings\CompanyBusinessSettings;
use App\Settings\CompanyIdentitySettings;
use App\Settings\CompanyLocaleSettings;
use App\Settings\CompanyPrivacySettings;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Tabbed company settings (core.company-settings/settings-tabs, ui-strategy
 * row #7): Identity / Locale / Business / Privacy, each tab bound to one
 * spatie settings class and saved independently. Owner-only.
 *
 * @property-read Schema $identityForm
 * @property-read Schema $localeForm
 * @property-read Schema $businessForm
 * @property-read Schema $privacyForm
 */
class CompanySettingsPage extends Page
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Company settings';

    protected static ?string $title = 'Company settings';

    protected static ?string $slug = 'company-settings';

    protected string $view = 'filament.app.pages.company-settings';

    /** @var array<string, mixed>|null */
    public ?array $identityData = [];

    /** @var array<string, mixed>|null */
    public ?array $localeData = [];

    /** @var array<string, mixed>|null */
    public ?array $businessData = [];

    /** @var array<string, mixed>|null */
    public ?array $privacyData = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        // Owner-only settings module (ADR 2026-06-11) on top of the standard
        // permission + module gate. core.settings is free-core (always on).
        return $user instanceof User
            && $user->hasRole('owner')
            && $user->can('core.settings.view')
            && app(BillingService::class)->hasModule('core.settings');
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        // Identity defaults come from the company row until first save.
        $identity = app(CompanyIdentitySettings::class)->toArray();
        $company = app(CompanyContext::class)->current();
        $identity['name'] = $identity['name'] !== '' ? $identity['name'] : $company->name;
        $identity['slug'] = $identity['slug'] !== '' ? $identity['slug'] : $company->slug;
        $this->identityForm->fill($identity);
        $this->localeForm->fill(app(CompanyLocaleSettings::class)->toArray());
        $this->businessForm->fill(app(CompanyBusinessSettings::class)->toArray());
        $this->privacyForm->fill(app(CompanyPrivacySettings::class)->toArray());
    }

    public function identityForm(Schema $schema): Schema
    {
        $companyId = app(CompanyContext::class)->currentId();

        return $schema
            ->statePath('identityData')
            ->components([
                Section::make('Identity')
                    ->description('Your workspace name, slug and brand color.')
                    ->footerActions([
                        Action::make('saveIdentity')->label('Save identity')->action('saveIdentity'),
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->label('Company name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('Workspace slug')
                            ->required()
                            ->alphaDash()
                            ->maxLength(64)
                            ->rule(Rule::unique('companies', 'slug')->ignore($companyId)),
                        ColorPicker::make('primary_color')
                            ->label('Primary color')
                            ->required()
                            ->regex('/^#[0-9a-fA-F]{6}$/'),
                    ]),
            ]);
    }

    public function localeForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('localeData')
            ->components([
                Section::make('Locale')
                    ->description('Timezone, language, dates and currency for the whole workspace.')
                    ->footerActions([
                        Action::make('saveLocale')->label('Save locale')->action('saveLocale'),
                    ])
                    ->schema([
                        Select::make('timezone')
                            ->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))
                            ->searchable()
                            ->required(),
                        Select::make('locale')
                            ->options(['en' => 'English', 'nl' => 'Nederlands'])
                            ->required(),
                        Select::make('date_format')
                            ->options([
                                'd-m-Y' => date('d-m-Y').' (d-m-Y)',
                                'Y-m-d' => date('Y-m-d').' (Y-m-d)',
                                'd/m/Y' => date('d/m/Y').' (d/m/Y)',
                                'm/d/Y' => date('m/d/Y').' (m/d/Y)',
                            ])
                            ->required(),
                        Select::make('currency')
                            ->options(['EUR' => 'EUR — Euro', 'USD' => 'USD — US Dollar', 'GBP' => 'GBP — British Pound'])
                            ->required(),
                        Select::make('currency_position')
                            ->label('Symbol position')
                            ->options(['before' => 'Before the amount (€ 12,50)', 'after' => 'After the amount (12,50 €)'])
                            ->required(),
                        Select::make('decimal_places')
                            ->options([0 => '0', 2 => '2'])
                            ->required(),
                    ]),
            ]);
    }

    public function businessForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('businessData')
            ->components([
                Section::make('Business')
                    ->description('Fiscal calendar and workweek rhythm.')
                    ->footerActions([
                        Action::make('saveBusiness')->label('Save business settings')->action('saveBusiness'),
                    ])
                    ->schema([
                        Select::make('fiscal_year_start_month')
                            ->label('Fiscal year starts in')
                            ->options(array_combine(range(1, 12), array_map(
                                fn (int $m): string => now()->startOfYear()->addMonths($m - 1)->format('F'),
                                range(1, 12),
                            )))
                            ->required(),
                        Select::make('week_start')
                            ->label('Week starts on')
                            ->options(['monday' => 'Monday', 'sunday' => 'Sunday'])
                            ->required(),
                        Select::make('holiday_calendar_country')
                            ->label('Public holiday calendar')
                            ->options(['NL' => 'Netherlands', 'BE' => 'Belgium', 'DE' => 'Germany'])
                            ->required(),
                    ]),
            ]);
    }

    public function privacyForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('privacyData')
            ->components([
                Section::make('Privacy')
                    ->description('Retention and GDPR contact for your workspace.')
                    ->footerActions([
                        Action::make('savePrivacy')->label('Save privacy settings')->action('savePrivacy'),
                    ])
                    ->schema([
                        Select::make('data_retention_months')
                            ->label('Data retention')
                            ->options([12 => '12 months', 24 => '24 months', 36 => '36 months', 60 => '60 months'])
                            ->required(),
                        TextInput::make('dsar_email')
                            ->label('DSAR contact email')
                            ->email()
                            ->maxLength(255),
                        Toggle::make('consent_logging_enabled')
                            ->label('Log consent events'),
                    ]),
            ]);
    }

    public function saveIdentity(): void
    {
        $data = $this->identityForm->getState();

        $settings = app(CompanyIdentitySettings::class);
        $settings->fill($data);
        $settings->save();

        // companies.name/slug stay the platform source of truth (login,
        // provisioning) — mirror the shared fields on save.
        app(CompanyContext::class)->current()->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        Notification::make()->success()->title('Identity saved')->send();
    }

    public function saveLocale(): void
    {
        $data = $this->localeForm->getState();

        $settings = app(CompanyLocaleSettings::class);
        $settings->fill($data);
        $settings->save();

        // SetLocale middleware + money formatting read the company row.
        app(CompanyContext::class)->current()->update([
            'timezone' => $data['timezone'],
            'locale' => $data['locale'],
            'currency' => $data['currency'],
        ]);

        Notification::make()->success()->title('Locale saved')->send();
    }

    public function saveBusiness(): void
    {
        $settings = app(CompanyBusinessSettings::class);
        $settings->fill($this->businessForm->getState());
        $settings->save();

        Notification::make()->success()->title('Business settings saved')->send();
    }

    public function savePrivacy(): void
    {
        $settings = app(CompanyPrivacySettings::class);
        $settings->fill($this->privacyForm->getState());
        $settings->save();

        Notification::make()->success()->title('Privacy settings saved')->send();
    }
}
