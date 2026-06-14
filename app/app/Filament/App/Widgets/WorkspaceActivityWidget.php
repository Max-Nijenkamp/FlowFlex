<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Models\Activity;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

/** Recent activity feed — mono timestamps + domain squares (design §12). */
class WorkspaceActivityWidget extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.app.widgets.workspace-activity';

    public static function canView(): bool
    {
        return Auth::guard('web')->check();
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'entries' => Activity::query()
                ->with('causer')
                ->latest()
                ->limit(8)
                ->get(),
        ];
    }
}
