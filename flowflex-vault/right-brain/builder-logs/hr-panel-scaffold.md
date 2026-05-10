---
type: builder-log
module: hr-panel-scaffold
domain: HR & People
panel: hr
phase: 2
started: 2026-05-10
status: complete
color: "#F97316"
left_brain_source: "[[MOC_HR]]"
last_updated: 2026-05-10
---

# Builder Log: HR Panel Scaffold

Left Brain source: [[MOC_HR]]

---

## Sessions

### Session 2026-05-10 (1) — HR panel scaffold

**Goal:** Create the HR Filament panel and all pre-Phase-2 scaffolding so module development can begin immediately.

**Built:**

Infrastructure:
- `app/Providers/Filament/HrPanelProvider.php` — HR panel at `/hr`, web guard, Violet `#7C3AED`, SetCompanyContext authMiddleware, SetLocale middleware, 5 nav groups (Employees, Leave, Payroll, Analytics, Settings)
- `bootstrap/providers.php` — HrPanelProvider registered
- `resources/css/filament/hr/theme.css` — Filament theme CSS with `@source` for `app/Filament/Hr/**/*.php`
- `vite.config.js` — `resources/css/filament/hr/theme.css` added to inputs; `npm run build` run

Directory structure:
- `app/Filament/Hr/Resources/` — empty, ready for Phase 2 resources
- `app/Filament/Hr/Pages/` — contains `Dashboard.php`
- `app/Filament/Hr/Widgets/` — empty, ready for Phase 2 widgets
- `resources/views/filament/hr/pages/` — ready for custom blade views

Pages:
- `app/Filament/Hr/Pages/Dashboard.php` — base dashboard, `canAccess()` = `auth()->check()`

Permissions (17 added to PermissionSeeder, total now 47):
- `hr.employees.*` — view-any, view, create, edit, delete
- `hr.onboarding.*` — view, manage
- `hr.leave.*` — view-any, request, approve, manage-policy
- `hr.payroll.*` — view, run, approve, export
- `hr.analytics.*` — view, export

Tests (4 added → 175 total):
- `tests/Feature/Filament/HrPanelTest.php` — redirects unauthenticated to login, dashboard loads for authenticated user, login page loads, panel distinct from app panel
- Updated permission count in PermissionSeederTest (30→47) and LocalSeederTest (30→47)

**Routes exposed:**
```
GET  /hr          → filament.hr.pages.dashboard
GET  /hr/login    → filament.hr.auth.login
POST /hr/logout   → filament.hr.auth.logout
GET  /hr/password-reset/...
```

**Decisions made:**
- Panel auth: `auth()->check()` (same as /app panel). Individual Phase 2 resources use `canAccess()` with `BillingService::enforceModuleAccess($company, 'hr.leave')` etc. for per-module subscription gating.
- No `access.hr-panel` permission needed — the panel is visible to all authenticated users; empty nav until HR modules are subscribed and their `canAccess()` returns true.

**Problems hit:**
- `protected static ?string $navigationIcon` causes `FatalError` in Filament 5 (type must be `BackedEnum|string|null`). Fix: use `getNavigationIcon(): string` method instead.
- HR theme not in Vite manifest → 500 on dashboard. Fix: `npm run build` after adding new entry to `vite.config.js`.
- Permission count: counted 46, actual 47 (10+20+17). Fixed in tests.

---

## Gaps Discovered

None — scaffold is complete and clean.
