<?php

declare(strict_types=1);

namespace App\Filament\Crm\Widgets;

use App\Contracts\Crm\DealServiceInterface;
use App\Models\Crm\Activity;
use App\Models\Crm\Deal;
use App\Models\User;
use App\Services\BillingService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/** CRM dashboard stats: open pipeline, weighted value, overdue tasks. */
class OverdueTasksWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('crm.deals.view-any')
            && app(BillingService::class)->hasModule('crm.deals');
    }

    /** @return array<int, Stat> */
    protected function getStats(): array
    {
        $openDeals = Deal::query()->where('status', 'open')->count();

        $weighted = app(DealServiceInterface::class)
            ->weightedPipelineValue()
            ->formatToLocale('nl_NL');

        $overdue = Activity::query()
            ->where('type', 'task')
            ->where('is_complete', false)
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->count();

        return [
            Stat::make('Open deals', (string) $openDeals),
            Stat::make('Weighted pipeline', $weighted),
            Stat::make('Overdue tasks', (string) $overdue)
                ->color($overdue > 0 ? 'danger' : 'success'),
        ];
    }
}
