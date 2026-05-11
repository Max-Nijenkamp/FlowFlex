<?php

declare(strict_types=1);

namespace App\Filament\Hr\Widgets;

use App\Models\HR\LeaveRequest;
use App\Support\Services\CompanyContext;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeaveStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $ctx = app(CompanyContext::class);

        if (! $ctx->hasCompany()) {
            return [];
        }

        $companyId = $ctx->currentId();

        $pendingRequests = LeaveRequest::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        $approvedThisMonth = LeaveRequest::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereYear('approved_at', now()->year)
            ->whereMonth('approved_at', now()->month)
            ->count();

        return [
            Stat::make('Pending Leave Requests', $pendingRequests)
                ->icon('heroicon-o-clock')
                ->color($pendingRequests > 0 ? 'warning' : 'success'),
            Stat::make('Approved This Month', $approvedThisMonth)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
