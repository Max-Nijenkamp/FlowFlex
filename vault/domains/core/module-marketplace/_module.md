---
domain: core
module: module-marketplace
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Module Marketplace

The activation UI for optional FlowFlex modules. Company owners and admins browse available modules, see pricing, and activate/deactivate with one click. Pure UI over [[../billing-engine/_module]] — no business logic of its own.

## Module-key

`core.marketplace`

**Priority:** v1-core
**Panel:** app
**Permission prefix:** `core.marketplace`
**Tables:** none of its own (reads billing's `module_catalog` (Sushi) + `company_module_subscriptions`)

**fires-events:** none · **consumes-events:** none

## Sibling notes

- [[architecture]] — the single custom page + how it delegates to BillingService
- [[security]] — permissions, owner-only access, tenancy
- [[decisions]] — owner-only (permission alone insufficient)
- Features: [[features/catalog-grid]] · [[features/activate-deactivate]]

No `data-model.md`, `api.md`, or `unknowns.md` — this module owns no tables, exposes no events/DTOs/contracts of its own, and every Build-Manifest file is verified present. The two distinct core features are broken out into `features/` notes; the summary stays folded below.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../billing-engine/_module]] (core.billing) | catalog + activate/deactivate + price preview all delegate to BillingService |

## Core Features (thin — folded here)

- Grid of all available modules grouped by domain; collapsible Section per domain.
- Per-module card: name, description, included features, per-user price, activation status.
- One-click activate — module immediately accessible to all panel users (within module-cache TTL).
- One-click deactivate — access gated, data retained. Free core modules shown as "included" without a deactivate option.
- Price preview: "At your current user count (15), this module adds €22.50/month".
- Activated-modules badge in the sidebar of the respective domain panel.
- Live search (name / key / domain, 300ms debounce) + active-count / €-month toolbar (2026-06-12 build sync).
- Module catalog backed by `calebporzio/sushi` static data model (`ModuleCatalog`).

## Data Model

None of its own. Reads `module_catalog` (Sushi) + `company_module_subscriptions`, both owned by [[../billing-engine/data-model]].

## DTOs / Services

Reuses `ActivateModuleData` from core.billing. Output DTO: `MarketplaceModuleData` — `module_key, name, domain, description, per_user_monthly_price_cents, price_preview_cents (price × active users), is_active_for_company`. No new services — the page calls `BillingServiceInterface::activateModule()` / `deactivateModule()`.

## Test Checklist

- [ ] Tenant isolation: company A's activation state (`company_module_subscriptions`) never renders on company B's marketplace
- [ ] Module gating: `ModuleMarketplacePage` hidden when `core.marketplace` inactive
- [ ] Page lists only `is_active` catalog modules
- [ ] Activate → module accessible (hasModule true) + appears as active card
- [ ] Deactivate → resource hidden in domain panel, data retained
- [ ] Price preview math matches user count × unit price (brick/money)
- [ ] Non-owner without permission sees grid read-only (no activate buttons) *(assumed)*
- [ ] Free core modules shown as "included" without deactivate option

## Build Manifest (corrected to flat paths)

```
app/Data/MarketplaceModuleData.php
app/Filament/App/Pages/ModuleMarketplacePage.php
resources/views/filament/app/pages/module-marketplace.blade.php
tests/Feature/Core/MarketplaceTest.php
```

All four verified present (`ModuleMarketplacePage` + `MarketplaceModuleData` confirmed in `app/`).

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | none (delegates) | core.billing | activate/deactivate call `BillingServiceInterface`; **core.billing** fires `ModuleActivated` — the marketplace itself fires nothing |
| consumes | none | — | reads billing catalog + subscription state synchronously; keeps no local copy |

Data ownership: module-marketplace owns and writes **no tables of its own** — it is pure UI over [[../billing-engine/_module|core.billing]]. It reads `module_catalog` (Sushi) + `company_module_subscriptions` read-only (owned by core.billing) and effects activation only by delegating to `BillingServiceInterface`, which writes billing's own tables and fires `ModuleActivated` ([[../../../security/data-ownership]]).

## Related

- [[../billing-engine/_module]] · [[../../../architecture/module-system]]
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../glossary]]
