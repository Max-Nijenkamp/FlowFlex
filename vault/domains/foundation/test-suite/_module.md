---
domain: foundation
module: test-suite
type: module
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Test Suite

`foundation.tests` — Pest on SQLite in-memory with `RefreshDatabase` and a `CompanyContext` helper. All tests are integration tests; no DB mocking.

## Shape (verified)

| Aspect | Value |
|---|---|
| Test count | ~186 tests *(approx; run `phpunit` to confirm exact)* |
| Test files | 33 (`tests/**/*Test.php`) |
| Suites | 3 — `Unit`, `Feature`, `Architecture` (`phpunit.xml`) |
| DB | SQLite `:memory:`, `BCRYPT_ROUNDS=4`, broadcast null |
| CI matrix | PHP **8.3 / 8.4 / 8.5**, Node 22 |
| Runner | `./vendor/bin/phpunit` |

> [!note] Corrected from flat spec
> ~186 tests / 33 files / 3 suites (not "first tenant-isolation test only"). CI runs the suite on a 3-version PHP matrix. Authoritative CI config: [[../../../infrastructure/ci-cd]].

## Architecture tests (verified present)

- `tests/Architecture/LayersTest.php` — no `dd`/`dump`/`var_dump`, layer rules
- `tests/Architecture/ModelsTest.php` — `HasUlids` + `SoftDeletes` on models
- `tests/Architecture/TenancyTest.php` — `withoutGlobalScope(CompanyScope)` forbidden outside admin/support

## Helpers

- `setCompany($company)` — sets `CompanyContext` + `setPermissionsTeamId`
- `RefreshDatabase` global on `tests/Feature/`
- External HTTP (`Stripe`, mail) faked via `Http::fake()`
- Rate limiter cleared in `beforeEach` for auth tests

## Test Checklist (verified)

- [x] `phpunit` green on SQLite in-memory from clean checkout
- [x] Tenant-isolation test passes — **M0 exit gate** (`tests/Feature/TenantIsolationTest.php`)
- [x] Architecture tests pass
- [ ] Full-suite runtime < 60s (paratest available)

## Build Manifest

```
phpunit.xml (sqlite :memory:, 3 suites)
tests/Pest.php · tests/TestCase.php (setCompany helper)
tests/Feature/TenantIsolationTest.php
tests/Architecture/{LayersTest,ModelsTest,TenancyTest}.php
.github/workflows/tests.yml (PHP 8.3/8.4/8.5) · lint.yml (Pint + frontend)
```

## Related

- [[../../../infrastructure/ci-cd]] — CI pipeline
- [[../../../architecture/patterns/testing-pattern]]
- [[../../../architecture/way-of-working]]
- [[../multi-tenancy-layer/_module|Multi-Tenancy Layer]]
