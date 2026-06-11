<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Spatie\Health\ResultStores\ResultStore;

class SystemHealthWidget extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.admin.widgets.system-health';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::guard('admin')->check();
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $latest = app(ResultStore::class)->latestResults();

        return [
            'checks' => $latest !== null ? $latest->storedCheckResults : collect(),
            'ranAt' => $latest !== null ? $latest->finishedAt : null,
        ];
    }
}
