---
domain: core
module: module-marketplace
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Module Marketplace — Architecture

Parent: [[_module]]

A single Filament custom page over [[../billing-engine/_module]]. No services, jobs, or state of its own.

| Artifact | Kind (ui-strategy) | Notes |
|---|---|---|
| `ModuleMarketplacePage` (`/app`) | #3-style custom page (grid, no drag) | domain sections, activate/deactivate buttons with confirm modal, price preview per card, live search + toolbar |

Backed by `resources/views/filament/app/pages/module-marketplace.blade.php`.

```mermaid
flowchart LR
    Page[ModuleMarketplacePage] -->|reads| Catalog[(ModuleCatalog sushi)]
    Page -->|reads| Subs[(company_module_subscriptions)]
    Page -->|MarketplaceModuleData| Card[per-domain module cards]
    Card -->|activate/deactivate| Svc[BillingServiceInterface]
    Svc -.->|busts cache, fires ModuleActivated| Billing[[billing-engine/_module]]
```

The page composes `MarketplaceModuleData` DTOs (price preview = unit price × active user count) and delegates all mutations to `BillingServiceInterface::activateModule()` / `deactivateModule()`. See [[../billing-engine/api]].
