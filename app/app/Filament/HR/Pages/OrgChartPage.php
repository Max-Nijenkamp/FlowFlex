<?php

declare(strict_types=1);

namespace App\Filament\HR\Pages;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\Employee;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * ui-strategy row #11 — tree view custom page, single query + in-memory assembly.
 */
class OrgChartPage extends Page
{
    protected string $view = 'filament.hr.pages.org-chart';

    /** Deferred first paint — blade shows a skeleton until wire:init fires. */
    public bool $readyToLoad = false;

    public function loadData(): void
    {
        $this->readyToLoad = true;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static string|UnitEnum|null $navigationGroup = 'Employees';

    protected static ?string $title = 'Org Chart';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.org.view')
            && app(BillingServiceInterface::class)->hasModule('hr.org');
    }

    /** @return list<array<string, mixed>> roots with nested children */
    public function getTree(): array
    {
        $employees = Employee::query()
            ->where('status', '!=', 'terminated')
            ->get(['id', 'first_name', 'last_name', 'job_title', 'manager_id'])
            ->map(fn (Employee $e) => [
                'id' => $e->id,
                'name' => $e->full_name,
                'title' => $e->job_title,
                'manager_id' => $e->manager_id,
                'children' => [],
            ])
            ->keyBy('id')
            ->all();

        $roots = [];
        foreach ($employees as $id => &$node) {
            if ($node['manager_id'] !== null && isset($employees[$node['manager_id']])) {
                $employees[$node['manager_id']]['children'][] = &$node;
            } else {
                $roots[] = &$node;
            }
        }

        return $roots;
    }
}
