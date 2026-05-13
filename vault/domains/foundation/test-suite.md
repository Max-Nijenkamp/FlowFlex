---
type: module
domain: Foundation
panel: (scaffold — no panel)
module-key: foundation.tests
status: planned
color: "#4ADE80"
---

# Test Suite

> Pest PHP test framework with SQLite in-memory isolation, Filament Livewire login patterns, CompanyContext seeding helpers, and factory conventions — tests are the gate that allows each build phase to proceed.

**Domain:** Foundation
**Module key:** `foundation.tests`

## What It Does

Every build phase must have a passing test suite before the next phase begins. Tests are written in Pest PHP. Feature tests use `RefreshDatabase` with SQLite in-memory to keep runs fast. Multi-tenancy tests verify that Company A's data is never visible when authenticated as Company B. Filament login is tested via Livewire component testing, not HTTP POST, because Filament 5 has no traditional POST login route. The global `Pest.php` file clears Filament's rate limiter before each Feature test to prevent silent failures from sequential login attempts.

## Features

### Core
- Pest PHP (v4+) with `pestphp/pest-plugin-laravel` and `pestphp/pest-plugin-livewire`
- All Feature tests use `RefreshDatabase` trait — database rolled back after each test
- Unit tests: no database — test models, services, traits, and DTOs with direct instantiation
- Feature test categories: Auth (guard isolation), MultiTenancy (scope verification), Filament (panel smoke tests), Seeders (idempotency)
- Directory: `tests/Unit/` (Models/, Services/), `tests/Feature/` (Auth/, MultiTenancy/, Filament/, Seeders/, {DomainName}/)

### Advanced
- Filament login tested via `Livewire::test(Login::class)->set('data.email', ...)->call('authenticate')` — never POST to `/app/login`
- Panel context required before Livewire login test: `Filament::setCurrentPanel(Filament::getPanel('app'))` in `beforeEach`
- Auth state isolation in `beforeEach`: `auth()->guard('admin')->logout(); auth()->guard('web')->logout();`
- Rate limiter cleared in `Pest.php`: `RateLimiter::clear('livewire-rate-limiter:' . sha1(Login::class . '|authenticate|127.0.0.1'))`
- `CompanyContext::set($company)` called in multi-tenancy tests before any tenant model query
- Factory conventions: every tenant model factory sets `company_id` via `state()` helper — never hardcoded
- Horizon auth tested via callback: `$callback = Horizon::$authUsing; $this->actingAs($admin, 'admin'); expect($callback(request()))->toBeTrue();`

### AI-Powered
- Test coverage threshold enforced at 80% — CI fails below this threshold
- Cross-company data leakage tests run on every PR: create records as Company A, assert invisible as Company B

## Data Model

```erDiagram
    test_helpers {
        string name PK
        string purpose
        string location
    }
```

| Helper | Location | Purpose |
|---|---|---|
| `createCompany()` | `tests/Helpers/` | Create a company with owner user |
| `actingAsOwner()` | `tests/Helpers/` | Authenticate as company owner with company context set |
| `actingAsAdmin()` | `tests/Helpers/` | Authenticate as FlowFlex admin |
| `CompanyContext::set()` | `App\Support\Services\CompanyContext` | Boot company scope for test request |

## Permissions

- `foundation.tests.run`
- `foundation.tests.configure`
- `foundation.tests.view-coverage`
- `foundation.tests.manage-factories`
- `foundation.tests.skip`

## Filament

- **Resource:** None (developer tooling, no UI)
- **Pages:** None
- **Custom pages:** None
- **Widgets:** None
- **Nav group:** N/A

## Related

- [[laravel-scaffold]]
- [[filament-panels]]
- [[multi-tenancy-layer]]
- [[docker-environment]]
