<?php

namespace App\Filament\Workspace\Pages\Settings;

use App\Models\NotificationPreference;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageNotificationPreferences extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notifications';

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.workspace.pages.settings.manage-notifications';

    public ?array $data = [];

    /**
     * All available notification types with their labels.
     */
    public static function notificationTypes(): array
    {
        return [
            'module.toggled'   => 'Module enabled / disabled',
            'company.updated'  => 'Company settings updated',
            'member.added'     => 'New team member added',
            'member.disabled'  => 'Team member disabled',
            'api_key.created'  => 'API key created',
            'api_key.revoked'  => 'API key revoked',
        ];
    }

    public function mount(): void
    {
        $tenant = auth('tenant')->user();

        $existing = NotificationPreference::where('tenant_id', $tenant->id)
            ->get()
            ->keyBy('notification_type');

        $formData = [];
        foreach (static::notificationTypes() as $type => $label) {
            $pref = $existing->get($type);
            $formData[$this->typeKey($type)] = [
                'is_enabled'  => $pref ? $pref->is_enabled : true,
                'mail'        => $pref ? in_array('mail', $pref->channels ?? []) : false,
            ];
        }

        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        $components = [];

        foreach (static::notificationTypes() as $type => $label) {
            $key = $this->typeKey($type);

            $components[] = Section::make($label)
                ->compact()
                ->columns(3)
                ->schema([
                    Toggle::make("{$key}.is_enabled")
                        ->label('Enabled')
                        ->default(true)
                        ->inline(false),

                    Checkbox::make("{$key}.mail")
                        ->label('Email')
                        ->helperText('Receive this notification via email.'),
                ]);
        }

        return $schema->statePath('data')->components($components);
    }

    public function save(): void
    {
        $data   = $this->form->getState();
        $tenant = auth('tenant')->user();

        foreach (static::notificationTypes() as $type => $label) {
            $key   = $this->typeKey($type);
            $entry = $data[$key] ?? [];

            $channels = ['database'];
            if (! empty($entry['mail'])) {
                $channels[] = 'mail';
            }

            NotificationPreference::updateOrCreate(
                [
                    'tenant_id'         => $tenant->id,
                    'notification_type' => $type,
                ],
                [
                    'company_id' => $tenant->company_id,
                    'channels'   => $channels,
                    'is_enabled' => (bool) ($entry['is_enabled'] ?? true),
                ]
            );
        }

        Notification::make()
            ->success()
            ->title('Notification preferences saved')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save preferences')
                ->submit('save'),
        ];
    }

    private function typeKey(string $type): string
    {
        return str_replace(['.', '-'], '_', $type);
    }
}
