---
domain: ecommerce
module: products
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Products — Architecture

## Status Lifecycle

Product `status` is a plain enum string (no `spatie/laravel-model-states` machine specified *(assumed)*):

```
draft → active → archived
  ↑───────┘ (re-activate)
```

- `draft` — invisible on the public storefront + public search.
- `active` — sellable, indexed for storefront browse/search.
- `archived` — hidden from storefront but retained for order history and reporting.

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Support\Ecommerce\ProductStock` | support helper | Single stock API. `available/reserve/release/deduct` — delegates to `operations.inventory` `StockService` when `ops_item_id` is set, otherwise reads/writes the internal `stock_quantity` field. |
| `App\Providers\Ecommerce\EcommerceServiceProvider` | provider | Registers panel + bindings. |

`ProductStock` is the **only** bridge to operations stock — no other class touches `ops_*` tables ([[../../../../security/data-ownership]]).

## Events

None fired or consumed directly by the catalogue. Orders fire `CheckoutCompleted`; products only supply the priced/stocked lines. See [[../../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Catalogue

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `EcProductResource` | #1 CRUD resource | tweaks: state-badge-column (draft/active/archived), custom-header-actions (publish / archive) | Tiptap description (purified), Media Library gallery; status + category filters |
| `EcCategoryResource` | #1 CRUD resource | (no tweaks — standard CRUD) | cycle-checked parent; tree parent select via `codewithdennis/filament-select-tree` *(assumed)* |

**Public storefront (Vue + Inertia):**

- Storefront browse/search is rendered by the [[../../storefront/_module|storefront]] module (Vue + Inertia, [[../../../architecture/ui-strategy]] row #16), reading only `status = active` products — not a Filament artifact here.

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('ecommerce.products.view-any') && BillingService::hasModule('ecommerce.products')`
per [[../../../architecture/filament-patterns]] #1. Both resources are standard CRUD (no custom pages). The public storefront surface declares its guest guard in [[../../storefront/_module|storefront]], not here.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Product / category CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Publish / archive status change | Optimistic | plain enum flip (no state-machine, no money/stock mutation) — `updated_at` stale-check ([[../../../architecture/patterns/optimistic-locking]]) |
| Internal `stock_quantity` decrement (non-ops products) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on `ec_products` — oversell prevention; see [[../../orders/_module|orders]] for the order-side reservation. Ops-backed stock is decremented inside `operations.inventory`'s own pessimistic path via `StockService`, not here |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

Meilisearch (`laravel/scout`): `name`, `description` (stripped), `sku`, `category`. Public storefront search filters `status = active` + company. No realtime.

## Jobs & Scheduling

None. Bulk import is handled via `core.import` when active.
