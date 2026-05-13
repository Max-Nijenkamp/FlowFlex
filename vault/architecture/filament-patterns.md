---
type: architecture
category: pattern
color: "#A78BFA"
---

# Filament 5 Patterns

Critical non-obvious patterns for building with Filament 5 in FlowFlex. Read this before writing any resource, page, widget, or panel provider. Each item here reflects a real pitfall that causes silent failures or hard-to-diagnose bugs.

---

## 1. canAccess() on Every Resource and Page

Every Filament resource and every custom page must implement `canAccess()`. This is the only mechanism that prevents unauthorised users (wrong permissions or inactive module subscription) from seeing navigation links and accessing URLs.

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

If `canAccess()` is omitted, the resource is visible to every authenticated user regardless of their role or module subscription. This is a security defect and a UX defect simultaneously.

---

## 2. View Property — Non-Static on Custom Pages

In Filament 5, the `$view` property on custom page classes must be declared as an **instance property**, not a static property:

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

The static declaration compiles without error but Filament 5 does not read it when rendering the page. The page renders blank or throws a missing view exception.

---

## 3. getSlug() Signature on Custom Pages

Custom pages that need a specific URL slug must implement `getSlug()` with the exact Filament 5 signature including the nullable `?Panel` parameter:

```php
// Correct — Filament 5 signature
public static function getSlug(?Panel $panel = null): string
{
    return 'kanban-board';
}

// Wrong — missing parameter causes interface mismatch in some versions
public static function getSlug(): string
{
    return 'kanban-board';
}
```

Without the `?Panel $panel = null` parameter, the method does not correctly override the parent in all PHP 8.4 covariance scenarios.

---

## 4. Navigation Property Types

Filament 5 changed the type hints on several navigation properties. Using the old types causes PHP 8.4 type errors:

```php
// Correct — Filament 5 types
protected static string|UnitEnum|null $navigationGroup = 'Employees';
protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
protected static BackedEnum|string|null $activeNavigationIcon = 'heroicon-s-users';

// Wrong — Filament 4 types
protected static ?string $navigationGroup = 'Employees';
protected static ?string $navigationIcon = 'heroicon-o-users';
```

Heroicons strings (`'heroicon-o-users'`) still work — the type is union, not an enum requirement. But the property declaration must use the new union type syntax.

---

## 5. Panel Provider Pattern

Each domain gets its own `PanelProvider` class. The standard structure:

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
            ->middleware([
                'web',
                SetLocale::class,
            ])
            ->authMiddleware([
                SetCompanyContext::class,
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/hr/theme.css');
    }
}
```

Registered in `bootstrap/providers.php`.

---

## 6. Theme CSS and Vite Registration

Each panel requires its own Tailwind CSS theme file. Without it, Filament loads its default theme and ignores the panel's domain colour.

File location: `resources/css/filament/{panel}/theme.css`

The file must be registered in `vite.config.js` as a separate input:

```js
// vite.config.js
input: [
    'resources/css/app.css',
    'resources/css/filament/hr/theme.css',
    'resources/css/filament/projects/theme.css',
    'resources/css/filament/finance/theme.css',
    // ... one per domain panel
],
```

And registered in the panel provider via `->viteTheme('resources/css/filament/hr/theme.css')`.

If the CSS file exists but is not registered in vite.config.js, the build step does not compile it and the panel loads without the custom theme.

---

## 7. Middleware Order

Middleware order in the panel provider matters. `SetCompanyContext` must run before any code that calls `app(CompanyContext::class)->current()`:

```php
->middleware([
    'web',          // session, CSRF, cookies
    SetLocale::class, // locale from user preference
])
->authMiddleware([
    Authenticate::class,     // must run first — establishes $user
    SetCompanyContext::class, // reads $user->company_id, sets CompanyContext
])
```

If `SetCompanyContext` runs before `Authenticate`, `auth()->user()` is null and the middleware throws a null dereference.

---

## 8. URL Slug Auto-Generation

Filament generates URL slugs from class names via `Str::kebab(class_basename($class))`. This is automatic — do not override it unless you need a specific slug. Knowing the pattern prevents surprises:

```
KanbanBoardPage          → /kanban-board-page
EmployeeProfilePage      → /employee-profile-page
DataImportPage           → /data-import-page
```

If you need a clean slug like `/kanban`, implement `getSlug()` with the correct Filament 5 signature (see point 3 above).

---

## 9. discoverResources() Path Convention

The `discoverResources()` call scans a directory and registers all resource classes in it. The `in` parameter is the filesystem path; the `for` parameter is the namespace prefix:

```php
->discoverResources(
    in: app_path('Filament/HR/Resources'),        // filesystem
    for: 'App\\Filament\\HR\\Resources',          // namespace
)
```

If the namespace does not match the actual class namespace, Filament silently skips the class. Always verify the class declares `namespace App\Filament\HR\Resources;` before assuming it will be auto-discovered.

---

## 10. Multi-Tenant Select Fields

Any Filament `Select` field that loads options from a model with `BelongsToCompany` automatically gets the company scope — which is correct. However, when building a field that intentionally loads across the current company's data (e.g. a manager assignment dropdown), you must not call `withoutGlobalScopes()`:

```php
// Correct — scoped to current company automatically via BelongsToCompany
Select::make('manager_id')
    ->relationship('manager', 'full_name')

// Wrong — bypasses company scope, shows all companies' users
Select::make('manager_id')
    ->options(User::withoutGlobalScopes()->pluck('full_name', 'id'))
```

The only context where loading without the scope is valid is the `/admin` panel for FlowFlex staff (e.g. an impersonation dropdown that lists all users across all companies).
