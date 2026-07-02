---
domain: ecommerce
module: products
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy | Notes |
|---|---|---|---|
| `EcProductResource` | Catalogue | simple-resource | Tiptap description, Media Library gallery, publish action, status/category filters. |
| `EcCategoryResource` | Catalogue | simple-resource | Tree (cycle-checked parent). |

Storefront browse/search is rendered by the [[../../storefront/_module|storefront]] module (Vue + Inertia), not here.

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.products.view-any')
        && BillingService::hasModule('ecommerce.products');
}
```

## Search & Realtime

Meilisearch (`laravel/scout`): `name`, `description` (stripped), `sku`, `category`. Public storefront search filters `status = active` + company. No realtime.

## Jobs & Scheduling

None. Bulk import is handled via `core.import` when active.
