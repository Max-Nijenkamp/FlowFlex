<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Support\Notifications\FlowFlexNotification;

class ModuleActivatedNotification extends FlowFlexNotification
{
    public function __construct(private readonly string $moduleName) {}

    public function notificationType(): string
    {
        return 'module-activated';
    }

    public function title(): string
    {
        return "{$this->moduleName} is now active";
    }

    public function body(): string
    {
        return "The {$this->moduleName} module was switched on for your workspace.";
    }
}
