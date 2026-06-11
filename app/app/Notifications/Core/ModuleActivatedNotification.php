<?php

declare(strict_types=1);

namespace App\Notifications\Core;

use App\Models\Core\ModuleCatalog;
use App\Support\Notifications\FlowFlexNotification;

class ModuleActivatedNotification extends FlowFlexNotification
{
    public function __construct(
        public readonly string $moduleKey,
    ) {
        parent::__construct();
    }

    public function title(): string
    {
        $name = (string) (ModuleCatalog::entry($this->moduleKey)['name'] ?? $this->moduleKey);

        return "Module activated: {$name}";
    }

    public function body(): string
    {
        return 'The module is now available to everyone in your workspace.';
    }
}
