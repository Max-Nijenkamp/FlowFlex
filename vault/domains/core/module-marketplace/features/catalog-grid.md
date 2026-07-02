---
domain: core
module: module-marketplace
feature: catalog-grid
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Catalog Grid (browse · search · price preview)

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

The browse surface of the marketplace: every available module rendered as cards grouped by domain, with live search and a per-card price preview.

## Behaviour

- Grid of all `is_active` catalog modules, grouped by domain in collapsible sections.
- Per-module card: name, description, included features, per-user price, and current activation status for this company.
- Live search over name / key / domain with a 300ms debounce; a toolbar shows active module count and total €/month (2026-06-12 build sync).
- Price preview per card: unit price × current active user count, e.g. "At your current user count (15), this module adds €22.50/month" (computed with `brick/money`).
- Free core modules render as "included" (no activate/deactivate control).

## UI

- **Kind**: custom-page
- **Page**: `ModuleMarketplacePage` (`/app`, `app/Filament/App/Pages/ModuleMarketplacePage.php` + `resources/views/filament/app/pages/module-marketplace.blade.php`).
- **Layout**: custom Filament page — collapsible `Section` per domain, each holding a grid of module cards; sticky toolbar (search box + active-count / €-month summary) above the grid.
- **Key interactions**:
  1. Owner opens the page → catalog reads compose `MarketplaceModuleData` DTOs (price preview = unit price × active users).
  2. Type in search → 300ms-debounced filter over name / key / domain.
  3. Collapse/expand a domain section.
  4. Read the per-card status + price preview (activate/deactivate handled in [[activate-deactivate]]).
- **States**: empty (search yields no modules → "No modules match" message) · loading (card skeletons while catalog/subscriptions load) · error (billing read failure → retry state) · selected (a card focused/hovered).
- **Gating**: `core.marketplace.view`; `canAccess()` additionally requires `BillingService::hasModule('core.marketplace')` and is owner-only in practice (see [[../security]] / [[../decisions]]). Non-owners without activation permission see the grid read-only *(assumed)*.

## Data

- Owns / writes: nothing — module-marketplace owns no tables of its own.
- Reads (read-only): `module_catalog` (Sushi `ModuleCatalog`) and `company_module_subscriptions`, both owned by [[../../billing-engine/data-model|core.billing]]; active user count for price preview via the billing/company read API.
- Cross-domain writes: none — this feature is read-only browse ([[../../../../security/data-ownership]]).

## Relations

- Consumes: no events at render — reads billing catalog + subscription state synchronously.
- Feeds: none (mutations live in [[activate-deactivate]]).
- Shared entity: **module catalog** + **company subscriptions** owned by [[../../billing-engine/_module|core.billing]] (read-only here).

## Unknowns

- Whether non-owners see the grid read-only vs not at all is `*(assumed)*` — see [[../_module]] Test Checklist.

## Related

- [[../_module|Module Marketplace]] · [[activate-deactivate]] · [[../../billing-engine/_module]]
