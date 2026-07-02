---
domain: foundation
module: test-suite
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Test Suite — Security

Parent: [[_module]]. The suite is itself a security control: the architecture tests are the automated enforcement of the tenant-isolation and data-ownership rules.

## Security enforced by tests

| Rule | Test | Effect |
|---|---|---|
| Tenant isolation | `TenantIsolationTest` — company A context returns zero company B rows | the M0 exit gate; a leak fails CI |
| No cross-tenant scope bypass | `TenancyTest` — `withoutGlobalScope(CompanyScope)` forbidden outside admin/support | stops accidental global reads at build time |
| Models carry isolation traits | `ModelsTest` — `HasUlids` + `SoftDeletes` on models | guarantees non-enumerable PKs + recoverable deletes |
| No data-leaking debug output | `LayersTest` — no `dd`/`dump`/`var_dump` in `app/` | prevents response-body disclosure |
| Queue keeps tenant | `QueueContextTest` — `WithCompanyContext` restores company + team | catches the null-tenant async leak |

These make [[../../../security/data-ownership]] and [[../../../security/tenancy-isolation]] **enforceable**, not
aspirational — the boundary is checked on every push.

## Test-environment hygiene

- SQLite `:memory:`, `BCRYPT_ROUNDS=4`, broadcast `null` — fast, isolated, no external side effects.
- External HTTP (Stripe, mail) faked via `Http::fake()` — no real calls, no leaked keys.
- Rate limiter cleared in `beforeEach` for auth tests — deterministic, no cross-test bleed.

> [!warning] UNVERIFIED — needs confirmation
> Whether CI secrets are injected via GitHub Actions secrets (not committed) and whether a secret-scanning
> step runs — the CI matrix is confirmed ([[../../../infrastructure/ci-cd]]); secret handling not re-read.

## Related

- [[_module]] · [[unknowns]]
- [[../../../security/tenancy-isolation]] · [[../../../security/data-ownership]] · [[../../../infrastructure/ci-cd]]
