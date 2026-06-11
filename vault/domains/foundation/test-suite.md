---
type: module
domain: Foundation
domain-key: foundation
panel: (scaffold)
module-key: foundation.tests
status: complete
priority: v1-core
depends-on: [foundation.scaffold, foundation.tenancy]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [testing]
tables: []
permission-prefix: ""
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Test Suite

Pest PHP framework configured with SQLite in-memory database, `RefreshDatabase`, and `CompanyContext` setup helpers. All tests are integration tests by default — no database mocking. Closes Foundation: the M0 exit gate requires the first tenant-isolation test passing here.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/laravel-scaffold\|foundation.scaffold]] | Pest installed, phpunit.xml |
| Hard | [[domains/foundation/multi-tenancy-layer\|foundation.tenancy]] | the helpers wrap CompanyContext |

---

## Core Features

- Pest PHP configured in `phpunit.xml` — SQLite in-memory overrides Docker PostgreSQL for tests
- `RefreshDatabase` applied globally to `tests/Feature/`
- `CompanyContext` helper available in all feature tests: `$this->setCompany($company)` (sets context + `setPermissionsTeamId`)
- Every model has a factory; factories always set `company_id`
- External HTTP services (Stripe, email) mocked via `Http::fake()` — no real HTTP calls in tests
- Rate limiter cleared in `beforeEach` for auth endpoint tests
- Module gating tests: `CompanyModuleSubscription::factory()` activates modules before assertions
- Architecture tests enforced from day one (traits, no-debug, layer rules — [[architecture/patterns/testing-pattern]])
- Coverage scope + what-not-to-test: per [[architecture/patterns/testing-pattern]] (80% of services/actions/listeners)

---

## Data Model

No additional tables — test infrastructure only.

## DTOs / Services & Actions / Filament / Permissions

None — test infrastructure.

---

## Test Checklist

(This module's deliverable IS tests — the checklist is the gate.)

- [ ] `php artisan test` runs green on SQLite in-memory from clean checkout
- [ ] `setCompany()` helper sets context + permission team id
- [ ] First tenant-isolation test passes (company A cannot read company B rows) — **M0 exit gate**
- [ ] Architecture tests pass (HasUlids/SoftDeletes/BelongsToCompany on models, no dd/dump)
- [ ] Full suite runtime < 60s (paratest available)

---

## Build Manifest

```
phpunit.xml (sqlite :memory:)
tests/Pest.php (RefreshDatabase binding, helper functions)
tests/TestCase.php (setCompany helper)
tests/Feature/Foundation/TenantIsolationTest.php
tests/Architecture/{LayersTest,ModelsTest}.php
```

---

## Related

- [[architecture/patterns/testing-pattern]]
- [[architecture/way-of-working]] — quality gates these tests feed
- [[domains/foundation/multi-tenancy-layer]]
