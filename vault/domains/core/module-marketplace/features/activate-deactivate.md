---
domain: core
module: module-marketplace
feature: activate-deactivate
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Activate / Deactivate Module

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

One-click activation and deactivation of optional modules, delegating entirely to `BillingServiceInterface`. The marketplace holds no business logic — it is pure UI over [[../../billing-engine/_module|core.billing]].

## Behaviour

- **Activate**: one click → confirm modal → `BillingServiceInterface::activateModule(ActivateModuleData)`. The module becomes accessible to all panel users within the module-cache TTL; the card flips to "active".
- **Deactivate**: one click → confirm modal → `BillingServiceInterface::deactivateModule()`. Access is gated off; **data is retained**. Free core modules have no deactivate control (shown as "included").
- All mutation goes through billing; the marketplace never writes subscription rows itself. Activation busts the module cache and fires `ModuleActivated` on the billing side.

## UI

- **Kind**: custom-page
- **Page**: `ModuleMarketplacePage` (`/app`) — the activate/deactivate action lives on each card of the [[catalog-grid|catalog grid]].
- **Layout**: per-card primary button ("Activate" / "Deactivate") with a confirmation modal; free core modules render an "included" badge instead of a button.
- **Key interactions**:
  1. Owner clicks **Activate** on a card → confirm modal → `activateModule()` → card flips to active, sidebar badge appears in the domain panel (within cache TTL).
  2. Owner clicks **Deactivate** → confirm modal → `deactivateModule()` → module hidden in its domain panel, data retained.
- **States**: empty (n/a — action attaches to grid cards) · loading (button spinner during the billing call) · error (billing exception → toast, card state unchanged) · selected (confirm modal open on a card).
- **Gating**: `core.billing.activate-module` / `core.billing.deactivate-module`; page `canAccess()` = `core.marketplace.view-any` + `BillingService::hasModule('core.marketplace')`; owner-only in practice — permission alone is insufficient (see [[../decisions]]).

## Data

- Owns / writes: nothing — module-marketplace owns no tables. `company_module_subscriptions` is written by **core.billing**, not here.
- Reads (read-only): current activation state from `company_module_subscriptions` to render the correct button.
- Cross-domain writes: performed **only** by delegating to `BillingServiceInterface` (the owning service writes its own tables) — the marketplace never touches billing tables directly ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: indirectly — the `activateModule()` call causes **core.billing** to fire `ModuleActivated`, consumed downstream by [[../../rbac/_module|core.rbac]] (widen assignable permission set) and core.notifications. The marketplace itself fires no events; it delegates.
- Shared entity: **company module subscriptions** owned by [[../../billing-engine/_module|core.billing]] — mutated only through its service.

## Unknowns

- None beyond the owner-vs-permission gate already recorded in [[../decisions]].

## Related

- [[../_module|Module Marketplace]] · [[catalog-grid]] · [[../../billing-engine/_module]] · [[../../billing-engine/api]]
