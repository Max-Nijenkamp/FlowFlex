---
type: architecture
category: filament
pattern-key: filament
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Filament 5 Patterns

Critical non-obvious patterns for Filament 5 in FlowFlex. Read this before writing any resource, page, widget, or panel provider. Each item reflects a real pitfall causing silent failures or hard-to-diagnose bugs.

---

## 1. canAccess() on Every Resource and Page

Every resource and every custom page must implement `canAccess()`. This is the only mechanism preventing unauthorised users from seeing navigation links and accessing URLs.

```php
// On resources
public static function canAccess(): bool
{
    return Auth::check()
        && Auth::user()->can('hr.employees.view-any')
        && BillingService::hasModule('hr.employees');
}

// On custom pages
public static function canAccess(): bool
{
    return Auth::check()
        && Auth::user()->can('hr.payroll.view-any')
        && BillingService::hasModule('hr.payroll');
}
```

If `canAccess()` is omitted, the resource is visible to every authenticated user regardless of role or module subscription. This is a security defect and UX defect simultaneously.

---

## 2. $view Property — Non-Static on Custom Pages

In Filament 5, `$view` on custom page classes must be an **instance property**, not static:

```php
// Correct — Filament 5
class KanbanBoardPage extends Page
{
    protected string $view = 'filament.projects.pages.kanban-board';
}

// Wrong — Filament 4 pattern, silently ignored in Filament 5
class KanbanBoardPage extends Page
{
    protected static string $view = 'filament.projects.pages.kanban-board';
}
```

The static declaration compiles without error but Filament 5 does not read it. Page renders blank or throws a missing view exception.

---

## 3. getSlug() Signature on Custom Pages

```php
// Correct — Filament 5 signature
public static function getSlug(?Panel $panel = null): string
{
    return 'kanban-board';
}

// Wrong — missing parameter causes interface mismatch
public static function getSlug(): string
{
    return 'kanban-board';
}
```

---

## 4. Navigation Property Types

```php
// Correct — Filament 5 types
protected static string|UnitEnum|null $navigationGroup = 'Employees';
protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
protected static BackedEnum|string|null $activeNavigationIcon = 'heroicon-s-users';

// Wrong — Filament 4 types
protected static ?string $navigationGroup = 'Employees';
protected static ?string $navigationIcon = 'heroicon-o-users';
```

Heroicons strings still work — the type is union, not an enum requirement. But the property declaration must use the new union type syntax.

---

## 5. Panel Provider Pattern

```php
class HrPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hr')
            ->path('hr')
            ->colors(['primary' => Color::hex('#7C3AED')])
            ->brandName('FlowFlex — HR & People')
            ->font('Inter')
            ->darkMode(Feature::Enabled)
            ->sidebarCollapsibleOnDesktop()
            ->authGuard('web')
            ->authModel(User::class)
            ->discoverResources(
                in: app_path('Filament/HR/Resources'),
                for: 'App\\Filament\\HR\\Resources',
            )
            ->discoverPages(
                in: app_path('Filament/HR/Pages'),
                for: 'App\\Filament\\HR\\Pages',
            )
            ->discoverWidgets(
                in: app_path('Filament/HR/Widgets'),
                for: 'App\\Filament\\HR\\Widgets',
            )
            ->middleware(['web', SetLocale::class])
            ->authMiddleware([
                Authenticate::class,
                SetCompanyContext::class,
            ])
            ->viteTheme('resources/css/filament/hr/theme.css');
    }
}
```

Registered in `bootstrap/providers.php`.

---

## 6. Theme CSS and Vite Registration

Each panel needs its own Tailwind CSS theme file at `resources/css/filament/{panel}/theme.css`.

Register in `vite.config.js`:

```js
input: [
    'resources/css/app.css',
    'resources/css/filament/hr/theme.css',
    'resources/css/filament/finance/theme.css',
    // one per domain panel
],
```

And in the panel provider: `->viteTheme('resources/css/filament/hr/theme.css')`.

If the CSS exists but is not in vite.config.js, the build step skips it silently.

---

## 7. Middleware Order

`Authenticate` must run before `SetCompanyContext`:

```php
->authMiddleware([
    Authenticate::class,     // establishes $user first
    SetCompanyContext::class, // reads $user->company_id
])
```

If `SetCompanyContext` runs before `Authenticate`, `auth()->user()` is null and the middleware throws a null dereference.

---

## 8. URL Slug Auto-Generation

Filament generates slugs from class names: `KanbanBoardPage → /kanban-board-page`. Use `getSlug()` (see item 3) when you need a clean slug like `/kanban`.

---

## 9. discoverResources() Path Convention

```php
->discoverResources(
    in: app_path('Filament/HR/Resources'),   // filesystem path
    for: 'App\\Filament\\HR\\Resources',    // namespace prefix
)
```

If the namespace does not match the class declaration, Filament silently skips the class. Verify the class declares `namespace App\Filament\HR\Resources;`.

---

## 10. Multi-Tenant Select Fields

```php
// Correct — scoped to current company automatically
Select::make('manager_id')
    ->relationship('manager', 'full_name')

// Wrong — bypasses company scope, shows all companies' users
Select::make('manager_id')
    ->options(User::withoutGlobalScopes()->pluck('full_name', 'id'))
```

`withoutGlobalScopes()` is only valid in the `/admin` panel for FlowFlex staff.
