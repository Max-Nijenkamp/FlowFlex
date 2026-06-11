<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Actions\Core\CompleteSetupAction;
use App\Actions\Core\SendInvitationAction;
use App\Contracts\Core\BillingServiceInterface;
use App\Data\Core\ActivateModuleData;
use App\Data\Core\CreateInvitationData;
use App\Exceptions\Core\ModuleAlreadyActiveException;
use App\Models\Core\ModuleCatalog;
use App\Settings\CompanyIdentitySettings;
use App\Settings\CompanyLocaleSettings;
use App\Support\Services\CompanyContext;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

/**
 * First-login onboarding for company owners. ui-strategy row #7.
 * Identity -> Locale -> Team (skippable) -> First Module (skippable).
 */
/**
 * @property-read Schema $form
 */
class SetupWizardPage extends Page
{
    protected string $view = 'filament.app.pages.setup-wizard';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Welcome to FlowFlex';

    public static function getSlug(?Panel $panel = null): string
    {
        return 'setup-wizard';
    }

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = Auth::guard('web')->user();

        return $user !== null
            && $user->hasRole('owner')
            && $user->company->setup_completed_at === null
            && app(BillingServiceInterface::class)->hasModule('core.setup');
    }

    public function mount(): void
    {
        $identity = app(CompanyIdentitySettings::class);
        $locale = app(CompanyLocaleSettings::class);

        $this->form->fill([
            'company_name' => $identity->name ?: app(CompanyContext::class)->current()->name,
            'primary_color' => $identity->primary_color,
            'timezone' => $locale->timezone,
            'locale' => $locale->locale,
            'currency' => $locale->currency,
            'invites' => [],
            'first_module' => null,
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('Identity')->schema([
                    TextInput::make('company_name')->required()->maxLength(200),
                    ColorPicker::make('primary_color')->required(),
                ]),
                Step::make('Locale')->schema([
                    Select::make('timezone')->options([
                        'Europe/Amsterdam' => 'Europe/Amsterdam',
                        'Europe/Brussels' => 'Europe/Brussels',
                        'Europe/Berlin' => 'Europe/Berlin',
                        'Europe/London' => 'Europe/London',
                        'UTC' => 'UTC',
                    ])->required(),
                    Select::make('locale')->options(['en' => 'English', 'nl' => 'Nederlands', 'de' => 'Deutsch'])->required(),
                    Select::make('currency')->options(['EUR' => 'EUR €', 'USD' => 'USD $', 'GBP' => 'GBP £'])->required(),
                ]),
                Step::make('Team')->schema([
                    Repeater::make('invites')->schema([
                        TextInput::make('email')->email()->required(),
                        Select::make('role')
                            ->options(fn () => Role::query()
                                ->where('team_id', getPermissionsTeamId())
                                ->where('name', '!=', 'owner')
                                ->pluck('name', 'name'))
                            ->required(),
                    ])->defaultItems(0)->addActionLabel('Add team member'),
                ]),
                Step::make('First module')->schema([
                    Select::make('first_module')
                        ->label('Pick a first module to activate (optional)')
                        ->options(fn () => collect(ModuleCatalog::entries())
                            ->filter(fn (array $m) => ! $m['is_free_core'] && $m['is_active'])
                            ->pluck('name', 'module_key'))
                        ->nullable(),
                ]),
            ])->submitAction(new HtmlString(view('filament.app.pages.partials.wizard-submit')->render())),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('finish'),
        ]);
    }

    public function finish(): void
    {
        $data = $this->form->getState();

        // Steps 1–2 → settings.
        $identity = app(CompanyIdentitySettings::class);
        $identity->name = $data['company_name'];
        $identity->primary_color = $data['primary_color'];
        $identity->save();

        $locale = app(CompanyLocaleSettings::class);
        $locale->timezone = $data['timezone'];
        $locale->locale = $data['locale'];
        $locale->currency = $data['currency'];
        $locale->save();

        // Step 3 → invitations (skippable).
        foreach ($data['invites'] ?? [] as $invite) {
            try {
                SendInvitationAction::run(new CreateInvitationData($invite['email'], $invite['role']));
            } catch (ValidationException) {
                // Duplicate/pending — skip silently inside the wizard.
            }
        }

        // Step 4 → first module (skippable).
        if (! empty($data['first_module'])) {
            try {
                app(BillingServiceInterface::class)->activateModule(new ActivateModuleData($data['first_module']));
            } catch (ModuleAlreadyActiveException) {
                // Already on — fine.
            }
        }

        CompleteSetupAction::run();

        Notification::make()->success()->title('Workspace ready — welcome aboard!')->send();
        $this->redirect('/app');
    }
}
