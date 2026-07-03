---
domain: foundation
module: filament-panels
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Filament Panels — Security

Parent: [[_module]]. The panels are the authenticated entry points; their guard split and middleware order are the first authorization boundary in the product.

## Permissions

None seeded by this module. Access is enforced by the **guard split** (`admin` vs `web`, non-overlapping) — the coarse authorization wall — plus per-resource `canAccess()` inside `/app`, which reads RBAC permissions owned by the domain modules that mount into the panel ([[../../../domains/core/rbac/_module]]). The panels themselves are always-on infrastructure and are not permission-gated. Login / 2FA / profile are Filament framework actions, not permission-gated commands.

## The guard split IS the boundary

| Panel | Guard | Model | Company-scoped? |
|---|---|---|---|
| `/admin` | `admin` | `Admin` | no — FlowFlex staff |
| `/app` | `web` | `User` | yes — CompanyScope active |

- `admin` and `web` **never overlap**: an `Admin` is rejected on `/app`, a tenant `User` is rejected on `/admin` (verified `PanelAuthTest`). This is the coarse authorization wall before any permission check.
- Fine-grained access inside `/app` is `canAccess()` per resource/page, reading RBAC permissions ([[../../../security/authn-authz]], [[../../../domains/core/rbac/_module]]).

## Middleware order (security-critical)

`Authenticate` → `SetCompanyContext` → `SetLocale` → `EnsureSubscriptionActive` → `RedirectToSetupWizard`,
run `isPersistent: true`.

- `Authenticate` **before** `SetCompanyContext` — context needs the resolved user ([[../../../architecture/filament-patterns]] #7).
- `isPersistent` keeps context on Livewire POSTs → avoids the null-team 403 family ([[../../../architecture/patterns/tenant-context-pitfalls]]). The failure mode is fail-closed (403), never cross-tenant.
- `EnsureSubscriptionActive` blocks suspended/cancelled companies before they reach resources.

## Login hardening

- Filament default login rate limiting on both panels (throttled brute-force). Limit values: [[../../../architecture/security]].
- 2FA available on `/app` (`AppAuthentication::make()->recoverable()`); password reset + email verification enabled.

## Filament tenant-scoping caveat

> [!warning] Filament does **not** auto-scope relation-manager records or modal `createOption` forms to the
> tenant — a documented framework seam ([[../../_opportunities]]). FlowFlex relies on `BelongsToCompany`'s
> `creating` hook to stamp `company_id`, but relation managers / nested create forms must be checked per
> resource on rebuild. Treat as an open hardening item.

> [!warning] UNVERIFIED — exact login throttle values and whether every resource's relation managers inherit
> tenant scope were not re-read from source.

## Related

- [[_module]] · [[unknowns]]
- [[../../../security/authn-authz]] · [[../../../architecture/filament-patterns]] · [[../../../architecture/patterns/tenant-context-pitfalls]]
- [[../multi-tenancy-layer/security]] · [[../../../domains/core/rbac/_module]]
