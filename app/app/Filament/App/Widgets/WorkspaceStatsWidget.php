<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalog;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/** Workspace KPI cells — the switchboard math, live (design §12/§20). */
class WorkspaceStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::guard('web')->check();
    }

    protected function getStats(): array
    {
        $activeKeys = CompanyModuleSubscription::query()
            ->whereNull('deactivated_at')
            ->pluck('module_key');

        $users = User::query()->count();
        $perUserCents = $activeKeys->sum(fn (string $key): int => ModuleCatalog::priceCents($key));
        $monthlyCents = $perUserCents * $users;

        $euro = fn (int $cents): string => '€'.number_format($cents / 100, 2, ',', '.');

        return [
            Stat::make('Modules on', (string) $activeKeys->count())
                ->description('+ core platform, always free'),
            Stat::make('Active users', (string) $users)
                ->description('everyone who can sign in'),
            Stat::make('Per user / month', $euro($perUserCents))
                ->description('Σ of active module prices'),
            Stat::make('Next invoice', $euro($monthlyCents))
                ->description($euro($perUserCents).' × '.$users.' users'),
        ];
    }
}
