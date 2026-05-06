<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class ModuleToggledNotification extends FlowFlexNotification
{
    public function __construct(
        public readonly string $moduleName,
        public readonly bool $enabled,
    ) {}

    public function notificationType(): string
    {
        return 'module.toggled';
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->enabled
                ? "{$this->moduleName} module enabled"
                : "{$this->moduleName} module disabled",
            'body'  => $this->enabled
                ? "The {$this->moduleName} module has been activated for your workspace."
                : "The {$this->moduleName} module has been deactivated for your workspace.",
            'icon'  => 'heroicon-o-puzzle-piece',
            'color' => $this->enabled ? 'success' : 'warning',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->enabled
            ? "{$this->moduleName} module enabled"
            : "{$this->moduleName} module disabled";

        $line = $this->enabled
            ? "The {$this->moduleName} module has been activated for your workspace."
            : "The {$this->moduleName} module has been deactivated for your workspace.";

        return (new MailMessage)
            ->subject($subject)
            ->line($line)
            ->line('You can manage your modules from the workspace settings.');
    }
}
