---
domain: core
module: workspace-hub
feature: domain-launcher
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Domain Launcher

The tile grid at the heart of the hub.

- One tile per accessible domain: icon, name, one-line descriptor, the domain's colour (per [[../../../../product/brand]] / domain colours).
- Ordered by *(assumed)* recency/favourites first, then alphabetical — see [[../unknowns]].
- Selecting a tile enters that domain's workspace (route/panel — see [[../architecture]]).
- States: loaded grid · empty (no active domains → marketplace CTA for owners, "ask admin" for others) ·
  skeleton on load ([[../../../../architecture/patterns/perceived-performance]], [[../../../../architecture/patterns/ux-states]]).

## UI

- **Kind**: custom-page
- **Page**: Workspace Hub launcher — the tenant's default post-login route (`custom-pages` pattern). Single/multi-panel routing shape is an open question (see [[../unknowns]]).
- **Layout**: responsive tile grid, one tile per accessible domain (icon, name, one-line descriptor, domain colour). Identity chrome (company/user, switch account, settings, sign out) around the grid.
- **Key interactions**:
  1. Tenant user authenticates → lands on the hub.
  2. Hub computes tiles = *company active modules ∩ user access permissions* (see [[../architecture]]).
  3. User selects a tile → enters that domain's workspace (route/panel).
  4. Ordering *(assumed)*: recency/favourites first, then alphabetical.
- **States**: empty (no active domains → marketplace CTA for owners, "ask your admin" for non-owners) · loading (skeleton grid per [[../../../../architecture/patterns/perceived-performance]]) · error (lookup failure → retry/support state) · selected (tile focus/hover before entering).
- **Gating**: `core.hub.view` (granted to every tenant user); each tile additionally requires the domain's `access.<domain>` permission **and** that the company has the domain's module(s) active.

## Data

- Owns / writes: nothing — workspace-hub owns **no tables** (`tables: []`). It is a pure read/compose surface.
- Reads (read-only): `company_module_subscriptions` / `ModuleCatalog` from [[../../billing-engine/_module|core.billing]] (activation), and Spatie permissions from [[../../rbac/_module|core.rbac]] (`access.<domain>`), both resolved under the current company context.
- Cross-domain writes: none — the hub never mutates another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: no domain events at render time — it queries activation + permissions synchronously per request. (A build may optionally consume `ModuleActivated`/`ModuleDeactivated` from core.billing to warm a cached tile list — *(assumed)*, not required.)
- Feeds: none — selecting a tile is client-side navigation, not a cross-domain event.
- Shared entity: **module activation** owned by [[../../billing-engine/_module|core.billing]] and **access permissions** owned by [[../../rbac/_module|core.rbac]] — both read-only here.

## Test Checklist

### Unit
- [ ] Tile set = active-modules ∩ `access.<domain>` permissions; a tile shows if ANY of the domain's modules is active and permitted
- [ ] Ordering places recency/favourites first, then alphabetical *(assumed)*

### Feature (Pest)
- [ ] A domain inactive for the company yields no tile even if the user holds `access.<domain>`
- [ ] A user lacking `access.<domain>` sees no tile even when the module is active
- [ ] Lookups run under the current company context — company A's user never sees company B's domains

### Livewire
- [ ] Empty state shows the marketplace CTA for owners and "ask your admin" for non-owners
- [ ] Hub denied to a user without `core.hub.view`; admin/staff never render the hub

## Related

- [[../_module|Workspace Hub]] · [[../architecture]]
