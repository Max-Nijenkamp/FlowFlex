<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Contracts\BillingServiceInterface;
use App\Models\NotificationPreference;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Per-user matrix: notification type × channel toggles. ui-strategy row #7.
 * Every authenticated user manages their own preferences — no extra permission.
 */
/**
 * @property-read Schema $form
 */
class NotificationPreferencesPage extends Page
{
    protected string $view = 'filament.app.pages.notification-preferences';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $title = 'Notification Preferences';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && app(BillingServiceInterface::class)->hasModule('core.notifications');
    }

    public function mount(): void
    {
        $user = Auth::guard('web')->user();
        $state = [];

        foreach (array_keys(self::types()) as $i => $type) {
            $preference = NotificationPreference::query()
                ->where('user_id', $user->id)
                ->where('notification_type', $type)
                ->first();

            $state["type_{$i}_in_app"] = $preference->in_app_enabled ?? true;
            $state["type_{$i}_email"] = $preference->email_enabled ?? true;
        }

        $this->form->fill($state);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $sections = [];

        foreach (array_values(self::types()) as $i => $label) {
            $sections[] = Section::make($label)
                ->columns(2)
                ->schema([
                    Toggle::make("type_{$i}_in_app")->label('In-app'),
                    Toggle::make("type_{$i}_email")->label('Email'),
                ]);
        }

        return $schema->components($sections);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save preferences')->submit('save'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();
        $state = $this->form->getState();

        foreach (array_keys(self::types()) as $i => $type) {
            NotificationPreference::query()->updateOrCreate(
                ['user_id' => $user->id, 'notification_type' => $type],
                [
                    'company_id' => $user->company_id,
                    'in_app_enabled' => (bool) ($state["type_{$i}_in_app"] ?? true),
                    'email_enabled' => (bool) ($state["type_{$i}_email"] ?? true),
                ],
            );
        }

        Notification::make()->success()->title('Preferences saved')->send();
    }

    /** @return array<string, string> type key => label */
    private static function types(): array
    {
        /** @var array<string, string> */
        return config('flowflex.notification_types', []);
    }
}
