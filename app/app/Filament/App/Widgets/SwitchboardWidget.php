<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalog;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

/** "Your switchboard" — active modules as zebra rows with prices (design §20). */
class SwitchboardWidget extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.app.widgets.switchboard';

    protected int|string|array $columnSpan = ['lg' => 2];

    public static function canView(): bool
    {
        return Auth::guard('web')->check();
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $rows = CompanyModuleSubscription::query()
            ->whereNull('deactivated_at')
            ->orderBy('module_key')
            ->pluck('module_key')
            ->map(fn (string $key): array => [
                'key' => $key,
                'name' => (string) (ModuleCatalog::entry($key)['name'] ?? $key),
                'domain' => explode('.', $key)[0],
                'cents' => ModuleCatalog::priceCents($key),
            ]);

        $users = User::query()->count();
        $perUserCents = $rows->sum('cents');

        return [
            'rows' => $rows,
            'users' => $users,
            'perUserCents' => $perUserCents,
            'monthlyCents' => $perUserCents * $users,
        ];
    }
}
