---
domain: foundation
module: multi-tenancy-layer
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Multi-Tenancy Layer — Security

Parent: [[_module]]. This is **the** tenant-isolation control. If it fails, one company reads or writes another company's data — the single highest-severity failure mode in the product. Threat model: [[../../../security/tenancy-isolation]].

## Permissions

None seeded by this module. It *provides* RBAC team-scoping — `SetCompanyContext` calls `setPermissionsTeamId(company_id)` so Spatie roles/permissions are evaluated per company — but defines no permission strings of its own. The assignable permission universe is seeded by [[../permissions-seed/_module|permissions-seed]] and managed by [[../../core/rbac/_module|core.rbac]].

## Isolation guarantees

| Guarantee | Mechanism |
|---|---|
| Every tenant query filtered by company | `CompanyScope` global scope on `BelongsToCompany` models — `WHERE company_id = current` on all reads |
| Writes stamped with the right company | `BelongsToCompany` `creating` hook auto-sets `company_id` — a caller cannot forge another company's id |
| Queued work keeps its tenant | `WithCompanyContext` restores `CompanyContext` **and** `setPermissionsTeamId()` in the worker from the job payload |
| RBAC scoped per company | `SetCompanyContext` calls `setPermissionsTeamId($company_id)` → Spatie roles/permissions team-scoped |
| Context missing = hard fail, not silent leak | `CompanyContext::current()` throws `MissingCompanyContextException` rather than returning global data |

## The escape hatch is guarded

- `withoutGlobalScope(CompanyScope)` is the only way to cross tenants — permitted **only** in `admin`/`support`
  code paths and **forbidden everywhere else by arch test** (`tests/Architecture/TenancyTest.php`). Any tenant
  code that calls it fails CI. This is the deliberate, audited cross-tenant door for FlowFlex staff.

## The Livewire persistence trap

`AppPanelProvider` runs the auth middleware with `isPersistent: true` so Livewire update POSTs re-run
`SetCompanyContext`. Without it, deferred tables/actions hit `MissingCompanyContextException` → the **null-team
403 family** ([[../../../architecture/patterns/tenant-context-pitfalls]]). This is both a UX and a security note:
the failure is fail-closed (403), never fail-open (cross-tenant read).

## Data-ownership tie-in

The layer is what makes [[../../../security/data-ownership]] enforceable: every write goes through the owning
service, which always runs under `CompanyContext`, so there is no side-door that skips company scope.

> [!warning] UNVERIFIED — needs confirmation
> The exact allow-list of namespaces permitted to call `withoutGlobalScope(CompanyScope)` (beyond "admin/support")
> and whether `MissingCompanyContextException` renders as 403 vs 500 were not re-read from source.

## Related

- [[_module]] · [[unknowns]]
- [[../../../security/tenancy-isolation]] · [[../../../security/data-ownership]] · [[../../../security/authn-authz]]
- [[../../../architecture/patterns/tenant-context-pitfalls]] · [[../../../architecture/multi-tenancy]]
