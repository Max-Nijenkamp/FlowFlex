<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Contracts\BillingServiceInterface;
use App\Settings\CompanyBusinessSettings;
use App\Settings\CompanyIdentitySettings;
use App\Settings\CompanyLocaleSettings;
use App\Settings\CompanyPrivacySettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Source of truth for workspace configuration. Tabs: Identity, Locale,
 * Business, Privacy. ui-strategy row #7 (wizard-style tabbed form).
 */
/**
 * @property-read Schema $form
 */
class CompanySettingsPage extends Page
{
    protected string $view = 'filament.app.pages.company-settings-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $title = 'Company Settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        // Owner-only: company-wide settings (founder decision 2026-06-11) —
        // permission alone is not enough.
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->hasRole('owner')
            && Auth::guard('web')->user()->can('core.settings.update')
            && app(BillingServiceInterface::class)->hasModule('core.settings');
    }

    public function mount(): void
    {
        $identity = app(CompanyIdentitySettings::class);
        $locale = app(CompanyLocaleSettings::class);
        $business = app(CompanyBusinessSettings::class);
        $privacy = app(CompanyPrivacySettings::class);

        $this->form->fill([
            'name' => $identity->name,
            'primary_color' => $identity->primary_color,
            'timezone' => $locale->timezone,
            'locale' => $locale->locale,
            'date_format' => $locale->date_format,
            'currency' => $locale->currency,
            'currency_position' => $locale->currency_position,
            'decimal_places' => $locale->decimal_places,
            'fiscal_year_start_month' => $business->fiscal_year_start_month,
            'week_start' => $business->week_start,
            'holiday_calendar_country' => $business->holiday_calendar_country,
            'data_retention_months' => $privacy->data_retention_months,
            'dsar_email' => $privacy->dsar_email,
            'consent_logging_enabled' => $privacy->consent_logging_enabled,
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Settings')->tabs([
                Tab::make('Identity')->schema([
                    TextInput::make('name')->required()->maxLength(200),
                    ColorPicker::make('primary_color')->required(),
                ]),
                Tab::make('Locale')->schema([
                    Select::make('timezone')->options(self::timezoneOptions())->searchable()->required(),
                    Select::make('locale')->options(['en' => 'English', 'nl' => 'Nederlands', 'de' => 'Deutsch'])->required(),
                    Select::make('date_format')->options([
                        'd-m-Y' => 'DD-MM-YYYY',
                        'Y-m-d' => 'YYYY-MM-DD',
                        'm/d/Y' => 'MM/DD/YYYY',
                    ])->required(),
                    Select::make('currency')->options(['EUR' => 'EUR €', 'USD' => 'USD $', 'GBP' => 'GBP £'])->required(),
                    Select::make('currency_position')->options(['before' => 'Before amount', 'after' => 'After amount'])->required(),
                    Select::make('decimal_places')->options([0 => '0', 2 => '2'])->required(),
                ]),
                Tab::make('Business')->schema([
                    Select::make('fiscal_year_start_month')
                        ->options(array_combine(range(1, 12), array_map(
                            fn (int $m) => now()->startOfYear()->addMonths($m - 1)->format('F'),
                            range(1, 12),
                        )))->required(),
                    Select::make('week_start')->options(['monday' => 'Monday', 'sunday' => 'Sunday'])->required(),
                    Select::make('holiday_calendar_country')->options(['NL' => 'Netherlands', 'BE' => 'Belgium', 'DE' => 'Germany'])->required(),
                ]),
                Tab::make('Privacy')->schema([
                    TextInput::make('data_retention_months')->numeric()->minValue(1)->maxValue(120)->required(),
                    TextInput::make('dsar_email')->email()->nullable(),
                    Toggle::make('consent_logging_enabled'),
                ]),
            ])->persistTabInQueryString(),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save settings')->submit('save')->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $identity = app(CompanyIdentitySettings::class);
        $identity->name = $data['name'];
        $identity->primary_color = $data['primary_color'];
        $identity->save();

        $locale = app(CompanyLocaleSettings::class);
        $locale->timezone = $data['timezone'];
        $locale->locale = $data['locale'];
        $locale->date_format = $data['date_format'];
        $locale->currency = $data['currency'];
        $locale->currency_position = $data['currency_position'];
        $locale->decimal_places = (int) $data['decimal_places'];
        $locale->save();

        $business = app(CompanyBusinessSettings::class);
        $business->fiscal_year_start_month = (int) $data['fiscal_year_start_month'];
        $business->week_start = $data['week_start'];
        $business->holiday_calendar_country = $data['holiday_calendar_country'];
        $business->save();

        $privacy = app(CompanyPrivacySettings::class);
        $privacy->data_retention_months = (int) $data['data_retention_months'];
        $privacy->dsar_email = $data['dsar_email'] ?: null;
        $privacy->consent_logging_enabled = (bool) $data['consent_logging_enabled'];
        $privacy->save();

        Notification::make()->success()->title('Settings saved')->send();
    }

    /** @return array<string, string> */
    private static function timezoneOptions(): array
    {
        $zones = ['Europe/Amsterdam', 'Europe/Brussels', 'Europe/Berlin', 'Europe/London', 'Europe/Paris', 'UTC'];

        return array_combine($zones, $zones);
    }
}
