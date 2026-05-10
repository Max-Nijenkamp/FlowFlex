<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Core\NotificationQuietHours;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class NotificationPreferencesPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'notification-preferences';

    public ?string $quietStart = null;
    public ?string $quietEnd   = null;

    public function getTitle(): string
    {
        return 'Notification Preferences';
    }

    public function getView(): string
    {
        return 'filament.app.pages.notification-preferences';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-bell';
    }

    public static function getNavigationLabel(): string
    {
        return 'Notifications';
    }

    public static function getNavigationGroup(): string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): int
    {
        return 20;
    }

    public static function canAccess(): bool
    {
        return auth()->check()
            && auth()->user()->checkPermissionTo('core.notifications.manage-own-preferences');
    }

    public function mount(): void
    {
        $user        = auth()->user();
        $quietHours  = NotificationQuietHours::where('user_id', $user->id)->first();
        $this->quietStart = $quietHours?->start_time;
        $this->quietEnd   = $quietHours?->end_time;
    }

    public function saveQuietHours(): void
    {
        if ($this->quietStart === null && $this->quietEnd === null) {
            NotificationQuietHours::where('user_id', auth()->id())->delete();
            Notification::make()->title('Quiet hours cleared')->success()->send();
            return;
        }

        NotificationQuietHours::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'start_time' => $this->quietStart,
                'end_time'   => $this->quietEnd,
                'timezone'   => auth()->user()->timezone ?? 'UTC',
            ]
        );

        Notification::make()->title('Quiet hours saved')->success()->send();
    }
}
