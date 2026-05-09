---
type: gap
severity: critical
category: architecture
status: fixed
color: "#F97316"
discovered: 2026-05-09
discovered_in: testing-standards
last_updated: 2026-05-09
---

# Gap: Company Scope Missing in Filament Panel Middleware

## Problem

`SetCompanyContext` middleware was only registered in the global `web` middleware group (via `bootstrap/app.php`). Filament panel providers define their OWN middleware stack that does NOT include the global `web` group by default — only what's explicitly listed.

Result: When a user accessed any Filament `/app` panel route, `CompanyContext` was never set. `CompanyScope` (`where company_id = X`) never activated. All tenant users could see ALL records from ALL companies.

## Impact

- **CRITICAL data leak**: Any tenant user could see other companies' users, roles, and (later) all business data
- Discovered via `WorkspacePanelTest::users list only shows users from same company` — "BobOnly" appeared in CompanyA user's view
- Would affect every future domain module resource built on `BelongsToCompany`

## Fix

Added `SetCompanyContext::class` to `->authMiddleware([...])` in `WorkspacePanelProvider`:

```php
->authMiddleware([
    Authenticate::class,
    SetCompanyContext::class,  // ← added
])
```

Also guarded the `Inertia::share()` call in `SetCompanyContext` to only run for actual Inertia requests:
```php
if (class_exists(\Inertia\Inertia::class) && $request->header('X-Inertia')) {
    \Inertia\Inertia::share('currentCompany', [...]);
}
```

## Prevention for Future Phases

Every new Filament panel provider MUST include `SetCompanyContext` in `authMiddleware`. This is documented in `left-brain/domains/00_foundation/testing-standards.md`.
