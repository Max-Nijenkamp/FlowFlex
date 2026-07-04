<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ModuleActivated;
use App\Models\ModuleCatalogEntry;
use App\Models\User;
use App\Notifications\ModuleActivatedNotification;
use App\Support\Jobs\Middleware\WithCompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyModuleActivatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(ModuleActivated $event): void
    {
        WithCompanyContext::restore($event->company_id);

        $name = ModuleCatalogEntry::query()
            ->where('module_key', $event->module_key)
            ->value('name') ?? $event->module_key;

        User::query()
            ->withoutGlobalScopes()
            ->where('company_id', $event->company_id)
            ->whereKey($event->activated_by)
            ->first()
            ?->notify(new ModuleActivatedNotification((string) $name));
    }
}
