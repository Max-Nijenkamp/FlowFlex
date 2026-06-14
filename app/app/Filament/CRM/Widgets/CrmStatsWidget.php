<?php

declare(strict_types=1);

namespace App\Filament\CRM\Widgets;

use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CrmStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.deals.view-any');
    }

    protected function getStats(): array
    {
        $openValue = (int) Deal::query()->where('status', 'open')->sum('value_cents');
        $openCount = Deal::query()->where('status', 'open')->count();

        $wonThisMonth = (int) Deal::query()
            ->where('status', 'won')
            ->whereBetween('actual_close_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('value_cents');

        $closedThisQuarter = Deal::query()
            ->whereIn('status', ['won', 'lost'])
            ->where('actual_close_date', '>=', now()->startOfQuarter())
            ->count();
        $wonThisQuarter = Deal::query()
            ->where('status', 'won')
            ->where('actual_close_date', '>=', now()->startOfQuarter())
            ->count();
        $winRate = $closedThisQuarter > 0 ? (int) round($wonThisQuarter / $closedThisQuarter * 100) : 0;

        $newLeads = Contact::query()
            ->whereIn('lifecycle_stage', ['lead', 'mql', 'sql'])
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return [
            Stat::make('Open pipeline', '€'.number_format($openValue / 100, 0))
                ->description($openCount.' open deals'),
            Stat::make('Won this month', '€'.number_format($wonThisMonth / 100, 0))
                ->color('success'),
            Stat::make('Win rate', $winRate.'%')
                ->description('Closed deals this quarter')
                ->color($winRate >= 50 ? 'success' : 'warning'),
            Stat::make('New leads', (string) $newLeads)
                ->description('This month'),
        ];
    }
}
