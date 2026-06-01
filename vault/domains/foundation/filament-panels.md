---
type: module
domain: Foundation
panel: (scaffold)
module-key: foundation.panels
status: planned
color: "#4ADE80"
---

# Filament Panels

Sets up the two base Filament 5 panels: `/admin` for FlowFlex staff and `/app` for tenant company users. Establishes panel conventions (guards, middleware order, theme registration) that all 21 domain panels inherit.

---

## Core Features

- `/admin` panel: `AdminPanelProvider`, `admin` guard, `Admin` model — no CompanyScope
- `/app` panel: `AppPanelProvider`, `web` guard, `User` model — CompanyScope active
- Middleware order: `Authenticate` before `SetCompanyContext` (see [[architecture/filament-patterns#7]])
- Theme CSS per panel registered in Vite config
- `sidebarCollapsibleOnDesktop()` on all panels
- `darkMode(Feature::Enabled)` on all panels
- `canAccess()` on every resource and page — see [[architecture/filament-patterns#1]]
- Domain panels registered in `bootstrap/providers.php`
- `bezhansalleh/filament-shield` installed for permission UI

---

## Data Model

No additional tables. Uses `admins` and `users` tables from [[domains/foundation/laravel-scaffold]].

---

## Filament

**`/admin` panel** — FlowFlex staff only:
- Company management (create, suspend, cancel tenants)
- User management (view all users across companies, impersonation)
- Module catalog management (prices, activation status)
- Billing overview

**`/app` panel** — tenant workspace entry point:
- Company settings
- Module marketplace
- User and role management
- Notifications inbox

---

## Panel Provider Template

```php
class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->colors(['primary' => Color::Slate])
            ->font('Inter')
            ->darkMode(Feature::Enabled)
            ->sidebarCollapsibleOnDesktop()
            ->authGuard('web')
            ->authModel(User::class)
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->middleware(['web', SetLocale::class])
            ->authMiddleware([Authenticate::class, SetCompanyContext::class])
            ->viteTheme('resources/css/filament/app/theme.css');
    }
}
```

---

## Related

- [[architecture/filament-patterns]]
- [[architecture/auth-rbac]]
- [[domains/foundation/multi-tenancy-layer]]
