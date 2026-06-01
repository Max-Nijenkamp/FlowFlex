---
type: module
domain: Core Platform
panel: app
module-key: core.marketplace
status: planned
color: "#4ADE80"
---

# Module Marketplace

The activation UI for optional FlowFlex modules. Company owners and admins browse available modules, see pricing, and activate/deactivate with one click.

---

## Core Features

- Grid of all available modules grouped by domain
- Per-module: name, description, included features, per-user price, activation status
- One-click activate — module immediately accessible to all panel users
- One-click deactivate — access gated, data retained
- Price preview: "At your current user count (15), this module adds €22.50/month"
- Activated modules badge in sidebar of their respective domain panel
- Module catalog backed by `calebporzio/sushi` static data model

---

## Filament

**`/app` panel:**
- `ModuleMarketplacePage` (custom page) — grid layout with domain sections

---

## Related

- [[domains/core/billing-engine]]
- [[product/pricing-model]]
