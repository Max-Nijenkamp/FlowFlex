---
type: module
domain: Foundation
panel: (scaffold)
module-key: foundation.tests
status: planned
color: "#4ADE80"
---

# Test Suite

Pest PHP framework configured with SQLite in-memory database, `RefreshDatabase`, and `CompanyContext` setup helpers. All tests are integration tests by default — no database mocking.

---

## Core Features

- Pest PHP configured in `phpunit.xml` — SQLite in-memory overrides Docker PostgreSQL for tests
- `RefreshDatabase` applied globally to `tests/Feature/`
- `CompanyContext` helper available in all feature tests via `$this->setCompany($company)`
- Every model has a factory; factories always set `company_id`
- External HTTP services (Stripe, email) mocked via `Http::fake()` — no real HTTP calls in tests
- Rate limiter cleared in `beforeEach` for auth endpoint tests
- Module gating tests: `CompanyModuleSubscription::factory()` activates modules before assertions

---

## Data Model

No additional tables — test infrastructure only.

---

## Filament

No resources. Test files in `tests/Feature/{Domain}/` and `tests/Unit/`.

---

## Related

- [[architecture/patterns/testing-pattern]]
- [[domains/foundation/multi-tenancy-layer]]
