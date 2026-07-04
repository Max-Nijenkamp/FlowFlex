<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Data\UpdateNotificationPreferencesData;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Services\NotificationPreferenceService;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

/**
 * Self-service per-type × per-channel notification toggles
 * (core.notifications/preferences). No view-any — own rows only.
 *
 * @property-read Schema $form
 */
class NotificationPreferencesPage extends Page
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $title = 'Notification preferences';

    protected static ?string $slug = 'notification-preferences';

    protected string $view = 'filament.app.pages.notification-preferences';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::user() instanceof User; // self-service: every member
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $state = [];
        foreach (array_keys(NotificationPreferenceService::TYPES) as $type) {
            $preference = NotificationPreference::query()
                ->where('user_id', $user->id)
                ->where('notification_type', $type)
                ->first();

            $key = str_replace('-', '_', $type);
            $state[$key.'_in_app'] = $preference === null || $preference->in_app_enabled;
            $state[$key.'_email'] = $preference === null || $preference->email_enabled;
        }

        $this->form->fill($state);
    }

    public function form(Schema $schema): Schema
    {
        $sections = [];

        foreach (NotificationPreferenceService::TYPES as $type => $description) {
            $key = str_replace('-', '_', $type);

            $sections[] = Section::make(str($type)->replace('-', ' ')->headline()->toString())
                ->description($description)
                ->compact()
                ->schema([
                    Toggle::make($key.'_in_app')->label('In-app'),
                    Toggle::make($key.'_email')->label('Email'),
                ])
                ->columns(2);
        }

        return $schema->statePath('data')->components([
            Section::make('Delivery preferences')
                ->description('Choose how each kind of update reaches you.')
                ->footerActions([
                    Action::make('save')->label('Save preferences')->action('save'),
                ])
                ->schema($sections),
        ]);
    }

    public function save(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $state = $this->form->getState();

        $preferences = [];
        foreach (array_keys(NotificationPreferenceService::TYPES) as $type) {
            $key = str_replace('-', '_', $type);
            $preferences[$type] = [
                'in_app' => (bool) ($state[$key.'_in_app'] ?? true),
                'email' => (bool) ($state[$key.'_email'] ?? true),
            ];
        }

        $data = new UpdateNotificationPreferencesData($preferences);

        foreach ($data->preferences as $type => $channels) {
            NotificationPreference::query()->updateOrCreate(
                ['user_id' => $user->id, 'notification_type' => $type],
                [
                    'company_id' => $user->company_id,
                    'in_app_enabled' => $channels['in_app'],
                    'email_enabled' => $channels['email'],
                ],
            );
        }

        Notification::make()->success()->title('Preferences saved')->send();
    }
}
