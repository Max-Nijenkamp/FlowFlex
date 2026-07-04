<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Contracts\BillingServiceInterface;
use App\Models\User;
use App\Models\UserInvitation;
use App\Support\Services\CompanyContext;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Dashboard stat tiles: the workspace at a glance. */
class WorkspaceOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $companyId = app(CompanyContext::class)->currentId();

        if ($companyId === null) {
            return [];
        }

        $activeModules = collect(app(BillingServiceInterface::class)->activeModules($companyId))
            ->reject(fn (string $key): bool => str_starts_with($key, 'core.'));

        $pendingInvites = UserInvitation::query()
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->count();

        return [
            Stat::make('Members', (string) User::query()->count())
                ->description($pendingInvites.' invites pending'),
            Stat::make('Active modules', (string) $activeModules->count())
                ->description('Manage them in the marketplace'),
            Stat::make('Workspace', app(CompanyContext::class)->current()->name)
                ->description('Since '.app(CompanyContext::class)->current()->created_at->format('M Y')),
        ];
    }
}
