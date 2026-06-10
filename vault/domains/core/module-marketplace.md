---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.marketplace
status: planned
priority: v1-core
depends-on: [core.billing]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: core.marketplace
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Module Marketplace

The activation UI for optional FlowFlex modules. Company owners and admins browse available modules, see pricing, and activate/deactivate with one click. Pure UI over [[domains/core/billing-engine]] — no business logic of its own.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] | catalog + activate/deactivate + price preview all delegate to BillingService |

---

## Core Features

- Grid of all available modules grouped by domain
- Per-module: name, description, included features, per-user price, activation status
- One-click activate — module immediately accessible to all panel users (within module-cache TTL)
- One-click deactivate — access gated, data retained
- Price preview: "At your current user count (15), this module adds €22.50/month"
- Activated modules badge in sidebar of their respective domain panel
- Module catalog backed by `calebporzio/sushi` static data model

---

## Data Model

None — reads `module_catalog` + `company_module_subscriptions` (owned by core.billing).

## DTOs

Reuses `ActivateModuleData` from core.billing. Output: `MarketplaceModuleData` — module_key, name, domain, description, per_user_monthly_price_cents, price_preview_cents (computed: price × active users), is_active_for_company.

## Services & Actions

None new — page calls `BillingServiceInterface::activateModule()` / `deactivateModule()`.

---

## Filament

**Nav group:** Billing

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ModuleMarketplacePage` | #3-style custom page (grid, no drag) | domain sections, activate/deactivate buttons with confirm modal, price preview per card |

---

## Permissions

`core.marketplace.view` · activation actions gated by `core.billing.activate-module` / `.deactivate-module` (owner/admin).

---

## Test Checklist

- [ ] Page lists only `is_active` catalog modules
- [ ] Activate button → module accessible (hasModule true) + appears as active card
- [ ] Deactivate → resource hidden in domain panel, data retained
- [ ] Price preview math matches user count × unit price (brick/money)
- [ ] Non-owner without permission sees grid read-only (no activate buttons) *(assumed)*
- [ ] Free core modules shown as "included" without deactivate option

---

## Build Manifest

```
app/Data/Core/MarketplaceModuleData.php
app/Filament/App/Pages/ModuleMarketplacePage.php
resources/views/filament/app/pages/module-marketplace.blade.php
tests/Feature/Core/MarketplaceTest.php
```

---

## Related

- [[domains/core/billing-engine]]
- [[product/pricing-model]]
- [[architecture/module-system]]
