<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Contracts\Core\BillingServiceInterface;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;
use UnitEnum;

/**
 * Green/red per health check for company owners. ui-strategy row #7,
 * polling 60s. Full dashboards (Pulse/Horizon) are staff-only in /admin.
 */
class SystemStatusPage extends Page
{
    protected string $view = 'filament.app.pages.system-status';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $title = 'System Status';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.health.view')
            && app(BillingServiceInterface::class)->hasModule('core.health');
    }

    /** @return list<array{name: string, status: string, message: string}> */
    public function getChecks(): array
    {
        $latest = app(ResultStore::class)->latestResults();

        if ($latest === null) {
            $this->callSilently(RunHealthChecksCommand::class);
            $latest = app(ResultStore::class)->latestResults();
        }

        return collect($latest->storedCheckResults ?? [])
            ->map(fn ($r) => [
                'name' => $r->label,
                'status' => $r->status,
                'message' => $r->shortSummary,
            ])
            ->all();
    }

    private function callSilently(string $command): void
    {
        try {
            Artisan::call($command);
        } catch (\Throwable) {
            // Status page must render even when a check crashes.
        }
    }
}
