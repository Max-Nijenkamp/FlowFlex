<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\User;
use App\Services\BillingService;
use App\Settings\CompanyBusinessSettings;
use App\Settings\CompanyIdentitySettings;
use App\Settings\CompanyLocaleSettings;
use App\Settings\CompanyPrivacySettings;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Company settings (core.company-settings/settings-tabs, ui-strategy row
 * #7): the Tabs component IS the card (owner call: no Section around tabs) — Identity /
 * Locale / Business / Privacy — each tab saving independently via its own
 * footer action. Owner-only.
 *
 * @property-read Schema $form
 */
class CompanySettingsPage extends Page
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Company settings';

    protected static ?string $title = 'Company settings';

    protected static ?string $slug = 'company-settings';

    protected string $view = 'filament.app.pages.company-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

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

        $company = app(CompanyContext::class)->current();

        // Identity defaults come from the company row until first save.
        $identity = app(CompanyIdentitySettings::class)->toArray();
        $identity['name'] = $identity['name'] !== '' ? $identity['name'] : $company->name;
        $identity['slug'] = $identity['slug'] !== '' ? $identity['slug'] : $company->slug;

        $this->form->fill([
            ...$identity,
            ...app(CompanyLocaleSettings::class)->toArray(),
            ...app(CompanyBusinessSettings::class)->toArray(),
            ...app(CompanyPrivacySettings::class)->toArray(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $companyId = app(CompanyContext::class)->currentId();

        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('settings')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Identity')
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
                                Actions::make([
                                    Action::make('saveIdentity')->label('Save identity')->action('saveIdentity'),
                                ])->columnSpanFull()->alignment(Alignment::End),
                            ])
                            ->columns(2),
                        Tab::make('Locale')
                            ->schema([
                                Select::make('timezone')
                                    ->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))
                                    ->searchable()
                                    ->required(),
                                Select::make('locale')
                                    ->label('Language')
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
                                Actions::make([
                                    Action::make('saveLocale')->label('Save locale')->action('saveLocale'),
                                ])->columnSpanFull()->alignment(Alignment::End),
                            ])
                            ->columns(2),
                        Tab::make('Business')
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
                                Select::make('max_upload_mb')
                                    ->label('Max upload size')
                                    ->options([10 => '10 MB', 25 => '25 MB', 50 => '50 MB', 100 => '100 MB'])
                                    ->required(),
                                Actions::make([
                                    Action::make('saveBusiness')->label('Save business settings')->action('saveBusiness'),
                                ])->columnSpanFull()->alignment(Alignment::End),
                            ])
                            ->columns(2),
                        Tab::make('Privacy')
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
                                Actions::make([
                                    Action::make('savePrivacy')->label('Save privacy settings')->action('savePrivacy'),
                                ])->columnSpanFull()->alignment(Alignment::End),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    protected function audit(string $tab): void
    {
        $user = Auth::user();

        app(AuditLogger::class)->log(
            'core.settings-updated',
            app(CompanyContext::class)->current(),
            $user instanceof User ? $user : null,
            ['tab' => $tab],
        );
    }

    /** @return array<string, mixed> */
    protected function validatedState(): array
    {
        return $this->form->getState();
    }

    public function saveIdentity(): void
    {
        $data = $this->validatedState();

        $settings = app(CompanyIdentitySettings::class);
        $settings->fill([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'logo_path' => $settings->logo_path,
            'favicon_path' => $settings->favicon_path,
            'primary_color' => $data['primary_color'],
        ]);
        $settings->save();

        // companies.name/slug stay the platform source of truth.
        app(CompanyContext::class)->current()->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        $this->audit('identity');

        Notification::make()->success()->title('Identity saved')->send();
    }

    public function saveLocale(): void
    {
        $data = $this->validatedState();

        $settings = app(CompanyLocaleSettings::class);
        $settings->fill([
            'timezone' => $data['timezone'],
            'locale' => $data['locale'],
            'date_format' => $data['date_format'],
            'currency' => $data['currency'],
            'currency_position' => $data['currency_position'],
            'decimal_places' => (int) $data['decimal_places'],
        ]);
        $settings->save();

        // SetLocale middleware + money formatting read the company row.
        app(CompanyContext::class)->current()->update([
            'timezone' => $data['timezone'],
            'locale' => $data['locale'],
            'currency' => $data['currency'],
        ]);

        $this->audit('locale');

        Notification::make()->success()->title('Locale saved')->send();
    }

    public function saveBusiness(): void
    {
        $data = $this->validatedState();

        $settings = app(CompanyBusinessSettings::class);
        $settings->fill([
            'fiscal_year_start_month' => (int) $data['fiscal_year_start_month'],
            'week_start' => $data['week_start'],
            'holiday_calendar_country' => $data['holiday_calendar_country'],
            'max_upload_mb' => (int) $data['max_upload_mb'],
        ]);
        $settings->save();

        $this->audit('business');

        Notification::make()->success()->title('Business settings saved')->send();
    }

    public function savePrivacy(): void
    {
        $data = $this->validatedState();

        $settings = app(CompanyPrivacySettings::class);
        $settings->fill([
            'data_retention_months' => (int) $data['data_retention_months'],
            'dsar_email' => $data['dsar_email'] ?? null,
            'consent_logging_enabled' => (bool) $data['consent_logging_enabled'],
        ]);
        $settings->save();

        $this->audit('privacy');

        Notification::make()->success()->title('Privacy saved')->send();
    }
}
