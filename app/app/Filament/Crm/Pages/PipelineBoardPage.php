<?php

declare(strict_types=1);

namespace App\Filament\Crm\Pages;

use App\Models\User;
use App\Services\BillingService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

/**
 * Kanban board host page (crm.pipeline/kanban-board, ui-strategy row #3).
 * All board state lives in the App\Livewire\Crm\PipelineBoard component;
 * this page is the gated shell.
 */
class PipelineBoardPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'Pipeline';

    protected static ?string $navigationLabel = 'Board';

    protected static ?string $title = 'Pipeline';

    protected static ?string $slug = 'pipeline';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.crm.pages.pipeline-board';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('crm.pipeline.view')
            && app(BillingService::class)->hasModule('crm.pipeline');
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
    }
}
