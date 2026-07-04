---
domain: foundation
module: test-suite
feature: tenant-aware-harness
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Tenant-Aware Test Harness (`setCompany` + RefreshDatabase)

Pest on in-memory SQLite with a one-line `setCompany()` helper, so every test can establish a tenant context without boilerplate.

## Behaviour

- `setCompany($company)` sets `CompanyContext` + `setPermissionsTeamId()` — the test equivalent of `SetCompanyContext`.
- `RefreshDatabase` global on `tests/Feature/`; SQLite `:memory:`, `BCRYPT_ROUNDS=4`, broadcast `null`.
- External HTTP (Stripe, mail) faked via `Http::fake()`; rate limiter cleared per test for auth cases.
- All integration tests — no DB mocking; every test exercises the real query path (so isolation bugs are caught, not mocked away).

## UI

- **Kind**: background (test infrastructure — no screen).

## Data

- Owns: no app tables (creates/tears down the test DB). Cross-domain writes: none.

## Relations

- Consumes: `CompanyContext` from [[../../multi-tenancy-layer/_module|multi-tenancy]]. Feeds: every domain's feature tests.
- Shared entity: the `TestCase`/`Pest.php` helpers.

## Test Checklist

### Unit
- [x] `setCompany($company)` sets `CompanyContext` + `setPermissionsTeamId`

### Feature (Pest)
- [x] A feature test scoped via `setCompany` sees only that company's rows
- [x] `Http::fake()` blocks real Stripe/mail calls; the rate limiter is cleared per auth test

## Unknowns

> [!warning] UNVERIFIED — whether factories default to a company automatically, and parallel-run tenant safety
> on `:memory:` — both known industry pain points ([[../../_opportunities]]). See [[../unknowns]].

## Related

- [[../_module|Test Suite]] · [[architecture-tests]] · [[../../../../architecture/patterns/testing-pattern]]
