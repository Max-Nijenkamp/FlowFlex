---
domain: foundation
module: multi-tenancy-layer
feature: persistent-context
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Persistent Request Context (`SetCompanyContext`, Livewire-safe)

Tenant context is set on every authenticated request — including Livewire update POSTs — so deferred tables and actions never lose their company.

## Behaviour

- `SetCompanyContext` middleware runs **after** `Authenticate`, reads `$user->company_id`, sets the `CompanyContext` singleton and `setPermissionsTeamId()`.
- Wired into `AppPanelProvider` auth middleware with `isPersistent: true` so Livewire's follow-up POSTs re-run it — otherwise deferred tables/actions throw `MissingCompanyContextException` → the **null-team 403 family** ([[../../../../architecture/patterns/tenant-context-pitfalls]]).
- `SetCompanyContextFromToken` is the Sanctum/API equivalent.
- Fail-closed: missing context → 403/exception, never a cross-tenant read.

## UI

- **Kind**: background (middleware — no screen). Its absence manifests as a UX bug (403 on table refresh), so
  it is validated by panel-auth tests ([[../../filament-panels/_module|filament-panels]]).

## Data

- Owns: no tables. Establishes the context every scoped query then reads.
- Cross-domain writes: none.

## Relations

- Consumes: the authenticated user (guard). Feeds: `CompanyScope`, RBAC team, every queued job's payload.

## Unknowns

> [!warning] UNVERIFIED — the `company_id = null` staff-as-tenant edge (`test@test.nl` is both admin and tenant
> owner) — how context resolves for a dual-identity login. See [[../unknowns]] + [[../../permissions-seed/_module]].

## Related

- [[../_module|Multi-Tenancy Layer]] · [[query-auto-scoping]] · [[../../filament-panels/_module|Filament Panels]] · [[../../../../architecture/patterns/tenant-context-pitfalls]]
