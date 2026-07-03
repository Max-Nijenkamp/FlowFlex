---
domain: core
module: billing-engine
feature: module-gating
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Module Gating

Parent: [[../_module]] · See [[../architecture]] · [[../api]]

The central access gate for every optional domain module.

- `BillingService::hasModule(string $key): bool` — called by every `canAccess()` in the product. Cached 5 min under `company:{id}:modules` ([[../../../../architecture/caching]]). Never call raw in a loop.
- `activateModule(ActivateModuleData)` — creates a `company_module_subscriptions` row, busts the cache (synchronously), syncs the Stripe item, fires `ModuleActivated`.
- `deactivateModule($moduleKey)` — sets `deactivated_at`, busts cache, removes Stripe item. Free core modules cannot be deactivated.
- Reactivation creates a new row → activation history preserved.
- Activation UI lives in [[../../module-marketplace/_module]]; staff-side per-company activation in [[../../staff-console/_module]].

## UI

- **Kind**: background (service gate)
- **Page**: background (no page of its own) — `BillingService::hasModule()` is a service method invoked by every `canAccess()` across the product. The activate/deactivate *screens* live in [[../../module-marketplace/_module]] (self-service) and [[../../staff-console/_module]] (staff).
- **Layout**: none directly. Its effect is that a gated module's panel/resource is present or absent from navigation for the whole company.
- **Key interactions**: unattended per-request check; the user-facing trigger is toggling a module in the marketplace, which calls `activateModule` / `deactivateModule`.
- **States**: n/a for the gate itself (boolean). Cache: `company:{id}:modules` resolves the value, busted synchronously on activate/deactivate.
- **Gating**: `core.billing.activate-module` / `core.billing.deactivate-module` guard the write operations (owner-only by default); the read (`hasModule`) is unauthenticated infrastructure.

## Data

- Owns / writes: `company_module_subscriptions` (activation rows; `deactivated_at` on deactivate; reactivation = new row, history preserved). Reads `module_catalog` (Sushi, owned here).
- Reads: none from other domains.
- Cross-domain writes: none — activation side effects reach other domains only via `ModuleActivated`. See [[../../../../security/data-ownership]].

## Relations

- Consumes: none.
- Feeds: `ModuleActivated` → consumed by [[../../notifications/_module]] (`NotifyModuleActivatedListener`, notifies owner/admins). Also read by [[../rbac/_module]] to scope assignable permissions to active modules.
- Shared entity: `module_catalog` (owned here); the active-module set is the shared fact every `canAccess()` and the RBAC permission scope depend on.

## Test Checklist

### Unit
- [ ] `hasModule` returns the cached value; a free core module cannot be deactivated

### Feature (Pest)
- [ ] `hasModule` true after `activateModule`, false after `deactivateModule`, within one request (cache bust)
- [ ] Tenant isolation: company A's activation is invisible to company B
- [ ] Double-activate guarded (pessimistic lock) — no duplicate subscription row; reactivation creates a new row preserving history
- [ ] `activateModule` fires `ModuleActivated`
