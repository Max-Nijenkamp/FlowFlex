---
domain: ecommerce
module: variants
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Variants — Architecture

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\Ecommerce\VariantService` | service | `generate(...)` — cartesian product of option values, SKU suffixing *(assumed: base-SKU-VALUE)*, skips existing combinations. `bulkUpdate(array $rows)` — batch price/stock/SKU. |

Order lines reference `variant_id` when a product has variants; the [[../../orders/_module|orders]] module validates that a variant is chosen and in stock.

## Events

None fired or consumed. Stock flows through `ProductStock` (products module). See [[../../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Catalogue (hosted on `EcProductResource`)

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `VariantsRelationManager` | #1 CRUD resource (relation manager on `EcProductResource`) | tweaks: inline-relation-repeater, custom-header-actions (generate variants) | Options repeater + bulk-edit variant table (SKU, price override, stock, image) |

**Access contract (mandatory):** the relation manager gates on
`canAccess() = Auth::user()->can('ecommerce.variants.manage') && BillingService::hasModule('ecommerce.variants')`
per [[../../../architecture/filament-patterns]] #1. It rides on the host `EcProductResource` page but declares its own module gate so variants can be inactive while products stay active. No custom pages, no public surface.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Option / variant CRUD (relation manager, bulk edit) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Matrix generation (`generate`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the parent product row so two concurrent generate runs cannot both insert the same combination — the `(product_id, option_values)` unique index is the backstop |
| Variant `stock_quantity` decrement (internal, non-ops) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on `ec_variants` — oversell prevention; ops-backed stock decremented inside `operations.inventory` via `ProductStock`/`StockService` |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

None. Variants are surfaced through their parent product's storefront page.

## Jobs & Scheduling

None.
