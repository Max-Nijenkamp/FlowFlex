---
type: architecture
category: patterns
pattern-key: custom-pages
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Custom Filament Pages

Not everything in FlowFlex is a standard Filament CRUD resource (list/create/edit). Some screens need custom layouts, interactive views, or domain-specific UX that the resource pattern cannot provide.

This pattern covers: how to build custom Filament pages, when to use them vs Vue + Inertia, and common pitfalls.

---

## When to Use Custom Filament Pages

| Screen type | Use |
|---|---|
| Standard list / create / edit / view | Filament Resource |
| Interactive view inside a panel (Kanban, Gantt, Calendar) | Custom Filament Page |
| Dashboard with widgets | Filament Dashboard Page |
| Wizard or multi-step form | Custom Filament Page |
| External-user-facing page (client portal, learner portal) | Vue 3 + Inertia |
| Public marketing page | Vue 3 + Inertia |

Rule of thumb: if it is behind auth and inside a Filament panel, use a custom Filament page. If it is external-facing or needs a completely custom design, use Vue + Inertia.

---

## Minimum Custom Page

```php
namespace App\Filament\HR\Pages;

use Filament\Pages\Page;

class LeaveCalendarPage extends Page
{
    // Instance property — NOT static (see filament-patterns#2)
    protected string $view = 'filament.hr.pages.leave-calendar';

    public static function getSlug(?Panel $panel = null): string
    {
        return 'leave-calendar';
    }

    public static function canAccess(): bool
    {
        return Auth::check()
            && Auth::user()->can('hr.leave.view-any')
            && BillingService::hasModule('hr.leave');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Leave';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-calendar-days';
    }
}
```

---

## Blade View for the Page

```blade
{{-- resources/views/filament/hr/pages/leave-calendar.blade.php --}}
<x-filament-panels::page>
    <x-filament::section>
        {{-- Livewire component or Alpine.js component here --}}
        @livewire('hr.leave-calendar-widget', ['month' => $this->month])
    </x-filament::section>
</x-filament-panels::page>
```

Use `<x-filament-panels::page>` as the wrapper — it applies the panel layout, sidebar, and header correctly.

---

## Page with Livewire State

```php
class KanbanBoardPage extends Page
{
    protected string $view = 'filament.projects.pages.kanban-board';

    public string $projectId;
    public array $columns = [];

    public function mount(string $projectId): void
    {
        $this->projectId = $projectId;
        $this->columns = $this->loadColumns();
    }

    public function moveCard(string $taskId, string $newColumnId): void
    {
        MoveTask::run(taskId: $taskId, columnId: $newColumnId);
        $this->columns = $this->loadColumns();
    }

    private function loadColumns(): array
    {
        // ...
    }
}
```

Livewire lifecycle works normally inside a Filament page class. `mount()` receives URL parameters.

---

## Widget-Based Dashboard Page

For pages composed of multiple widgets (e.g. Finance dashboard, Analytics overview):

```php
class FinanceDashboard extends Dashboard
{
    public static function getWidgets(): array
    {
        return [
            RevenueChartWidget::class,
            InvoiceStatsWidget::class,
            ExpenseBreakdownWidget::class,
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::check() && BillingService::hasModule('finance.dashboard');
    }
}
```

---

## Navigation Registration

Custom pages are auto-discovered by `discoverPages()` in the panel provider. They appear in the sidebar automatically if `getNavigationIcon()` and `getNavigationGroup()` are defined.

To hide a page from the sidebar but keep it accessible by URL:

```php
protected static bool $shouldRegisterNavigation = false;
```

---

## Common Pitfalls

**Static `$view` property** — silent failure. See [[architecture/filament-patterns#2]].

**Missing `canAccess()`** — page is visible to every authenticated user. See [[architecture/filament-patterns#1]].

**Wrong `getSlug()` signature** — URL routing fails. See [[architecture/filament-patterns#3]].

**Passing raw Eloquent collections to Blade** — always transform to DTOs or arrays before passing to the view. Blade templates should receive plain data, not live Eloquent models.
