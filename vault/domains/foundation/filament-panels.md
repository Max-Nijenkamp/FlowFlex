---
type: module
domain: Foundation
domain-key: foundation
panel: (scaffold)
module-key: foundation.panels
status: complete
priority: v1-core
depends-on: [foundation.scaffold, foundation.tenancy]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [panels, custom-pages]
tables: []
permission-prefix: ""
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Filament Panels

Sets up the two base Filament 5 panels: `/admin` for FlowFlex staff and `/app` for tenant company users. Establishes panel conventions (guards, middleware order, theme registration) that all 21 domain panels inherit.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/laravel-scaffold\|foundation.scaffold]] | Filament installed, `users`/`admins` models |
| Hard | [[domains/foundation/multi-tenancy-layer\|foundation.tenancy]] | `SetCompanyContext` middleware in `/app` auth stack |

---

## Core Features

- `/admin` panel: `AdminPanelProvider`, `admin` guard, `Admin` model — no CompanyScope
- `/app` panel: `AppPanelProvider`, `web` guard, `User` model — CompanyScope active
- Middleware order: `Authenticate` before `SetCompanyContext` (see [[architecture/filament-patterns]] #7)
- Theme CSS per panel registered in Vite config
- `sidebarCollapsibleOnDesktop()` + `darkMode(Feature::Enabled)` on all panels
- `canAccess()` on every resource and page — see [[architecture/filament-patterns]] #1
- Domain panels registered in `bootstrap/providers.php`
- `bezhansalleh/filament-shield` installed for permission UI

---

## Data Model

No additional tables. Uses `admins` and `users` from [[domains/foundation/laravel-scaffold]].

---

## Filament

This module creates the two panel shells only — resources land with Core Platform modules:

**`/admin` panel** — FlowFlex staff only (resources built in core modules + admin features):
- Company management (create, suspend, cancel tenants)
- User management (view all users across companies, impersonation)
- Module catalog management (prices, activation status)
- Billing overview

**`/app` panel** — tenant workspace entry point:
- Company settings · Module marketplace · User and role management · Notifications inbox

UI kinds for everything above: standard Filament resources ([[architecture/ui-strategy]] row #1).

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
            ->font('Instrument Sans') // Switchboard+ body face (brand.md)
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

## DTOs / Services & Actions

None — panel infrastructure.

## Permissions

None seeded here (permission strings ship with their modules). The guard split IS the authorization boundary: `admin` guard never overlaps `web`.

---

## Test Checklist

- [ ] `/admin` login works with Admin model; tenant `User` credentials rejected on `/admin`
- [ ] `/app` login works with User model; `Admin` credentials rejected on `/app`
- [ ] `SetCompanyContext` runs on every authenticated `/app` request (context set, team id set)
- [ ] Middleware order: unauthenticated `/app` request redirects to login without `MissingCompanyContextException`
- [ ] Both panel themes compile via Vite and load
- [ ] Dark mode toggle persists

---

## Build Manifest

```
app/Providers/Filament/AdminPanelProvider.php
app/Providers/Filament/AppPanelProvider.php
config/auth.php (admin guard + provider)
app/Http/Middleware/SetLocale.php
resources/css/filament/admin/theme.css
resources/css/filament/app/theme.css
vite.config.js (theme inputs)
bootstrap/providers.php (registrations)
tests/Feature/Foundation/PanelAuthTest.php
```

---


**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Add a note to the Filament panel spec that login endpoints on both panels enforce a login throttle (Laravel's default Filament login rate limit or an explicit `throttle` rule), and reference architecture/security.md for the limit values.

---

## Related

- [[architecture/filament-patterns]]
- [[architecture/auth-rbac]]
- [[architecture/domain-panels]]
- [[domains/foundation/multi-tenancy-layer]]
