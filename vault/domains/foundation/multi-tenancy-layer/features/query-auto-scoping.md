---
domain: foundation
module: multi-tenancy-layer
feature: query-auto-scoping
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Query Auto-Scoping (`CompanyScope` + `BelongsToCompany`)

Every read on a tenant model is auto-filtered to the current company; every create is auto-stamped with it. Developers never write `where('company_id', …)` by hand.

## Behaviour

- `BelongsToCompany` trait registers `CompanyScope` (global scope) → all queries get `WHERE company_id = current`.
- `creating` model hook auto-sets `company_id` from `CompanyContext::currentId()` — callers cannot forge another company's id.
- Reads with no context throw `MissingCompanyContextException` (fail-closed, never global-leak).
- Crossing tenants requires `withoutGlobalScope(CompanyScope)` — allowed only in admin/support, arch-test-enforced ([[../../test-suite/_module|test-suite]] `TenancyTest`).

## UI

- **Kind**: background (query-layer behaviour — no screen). Its effect is visible everywhere: every resource
  list in `/app` shows only the current company's rows.

## Data

- Owns: no tables. Applies to every `BelongsToCompany` model across all domains.
- Cross-domain writes: none — it is the mechanism that *prevents* them ([[../../../../security/data-ownership]]).

## Relations

- Consumes: the current company from `SetCompanyContext` ([[persistent-context]]).
- Feeds: every domain's data reads/writes; RBAC team scoping.

## Test Checklist

### Unit
- [ ] `creating` hook stamps `company_id` from `CompanyContext::currentId()`

### Feature (Pest)
- [ ] Company A context returns zero company B rows (`TenantIsolationTest`)
- [ ] A read with no context throws `MissingCompanyContextException` (fail-closed)
- [ ] `withoutGlobalScope(CompanyScope)` forbidden outside admin/support (`TenancyTest`)

## Unknowns

> [!warning] UNVERIFIED — the exact allow-list of code permitted to bypass the scope, and whether
> `LogsCompanyActivity` scoping is uniform. See [[../unknowns]].

## Related

- [[../_module|Multi-Tenancy Layer]] · [[persistent-context]] · [[queue-context]] · [[../../../../architecture/patterns/belongs-to-company]]
