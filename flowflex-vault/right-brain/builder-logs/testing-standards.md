---
type: builder-log
module: testing-standards
domain: Foundation
panel: n/a
phase: 0
started: 2026-05-09
status: complete
color: "#F97316"
left_brain_source: "[[testing-standards]]"
last_updated: 2026-05-09
---

# Builder Log: Testing Standards

Left Brain source: [[testing-standards]]

---

## Sessions

### Session 2026-05-09

**Goal:** Write Phase 0 Pest test suite; establish per-phase testing standards; fix all bugs discovered during test writing.

**Built:**

Factories:
- `database/factories/AdminFactory.php` — `Admin` model factory with superAdmin/support states
- `database/factories/CompanyFactory.php` — `Company` model factory with active/suspended states
- `database/factories/UserFactory.php` — Rewrote to match actual User fields (first_name, last_name, company_id, status, etc.)

Unit Tests:
- `tests/Unit/Models/AdminTest.php` — isSuperAdmin, canImpersonate methods
- `tests/Unit/Models/CompanyTest.php` — isActive, isSuspended methods
- `tests/Unit/Models/UserTest.php` — name accessor, isActive/isInvited/isDeactivated
- `tests/Unit/Services/CompanyContextTest.php` — set/get/clear/hasCompany/currentId/singleton

Feature Tests:
- `tests/Feature/Auth/AdminAuthTest.php` — login page, redirects, Livewire login, logout, guard isolation
- `tests/Feature/Auth/WorkspaceAuthTest.php` — same for web guard
- `tests/Feature/Auth/GuardIsolationTest.php` — admin cannot access app panel, web cannot access admin
- `tests/Feature/MultiTenancy/CompanyScopeTest.php` — scope applies, no cross-company leaks, auto-fills company_id
- `tests/Feature/MultiTenancy/CompanyContextTest.php` — service integration, singleton, exception on missing
- `tests/Feature/Filament/AdminPanelTest.php` — dashboard, Horizon auth callback, 403 checks
- `tests/Feature/Filament/WorkspacePanelTest.php` — users/roles resource, company scope in table
- `tests/Feature/Seeders/LocalSeederTest.php` — seeder creates records, idempotent, correct credentials

**Final result:** 74 tests, 113 assertions, 0 failures.

**Decisions made:**
- Filament Login forms tested via `Livewire::test(Login::class)->set('data.email', ...)->call('authenticate')` not via POST route (Filament 5 has no login POST route)
- Horizon test via callback not HTTP (controller-level middleware doesn't respect `actingAs` guard in tests)
- Rate limiter cleared in global `Pest.php` `beforeEach` to prevent test-order pollution
- Auth state cleared (`guard()->logout()`) before each Livewire auth test
- `Filament::setCurrentPanel()` called before each Livewire auth test

---

## Bugs Found and Fixed

### Bug 1: UserResource `last_login_at` datetime + default string
- **File:** `app/Filament/App/Resources/UserResource.php:111`
- **Problem:** `->dateTime()->default('Never')` causes Carbon to parse `'Never'`, throwing exception
- **Fix:** Changed `->default('Never')` to `->placeholder('Never')`
- **Severity:** High (500 error on /app/users)

### Bug 2: SetCompanyContext not in workspace panel middleware
- **File:** `app/Providers/Filament/WorkspacePanelProvider.php`
- **Problem:** `SetCompanyContext` middleware was only in global `web` group, not in Filament's `authMiddleware`. Filament panel requests don't go through the global `web` middleware stack.
- **Fix:** Added `SetCompanyContext::class` to `->authMiddleware([...])` in `WorkspacePanelProvider`
- **Impact:** Without this fix, ALL tenant data from all companies was visible to every logged-in user
- **Severity:** Critical (data leak)

### Bug 3: SetCompanyContext had unconditional Inertia::share()
- **File:** `app/Http/Middleware/SetCompanyContext.php`
- **Problem:** `Inertia::share()` called unconditionally; fails silently or causes errors in non-Inertia (Filament) context
- **Fix:** Added `if (class_exists(Inertia::class) && $request->header('X-Inertia'))` guard

---

## Gaps Discovered

None additional — see existing gaps in MOC_Gaps.

---

## Architectural Notes

**Test ordering pollution:** Pest runs all tests in the same PHP process. Rate limiters (Redis-backed) accumulate across tests. Auth guard state (`setUser`, `logout`) persists in memory between tests. Must clear both in `beforeEach`.

**Filament 5 Livewire test context:** `Livewire::test()` doesn't auto-detect the Filament panel. Must call `Filament::setCurrentPanel()` explicitly. Otherwise `Filament::auth()` returns the wrong guard and login tests either silently pass or silently fail.

**Company scope + Filament:** The `CompanyScope` only activates when `CompanyContext::currentId()` returns non-null. `SetCompanyContext` must be in the Filament panel's middleware, not just the global `web` group.
