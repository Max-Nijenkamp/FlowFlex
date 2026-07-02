---
domain: ecommerce
module: variants
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Host | ui-strategy | Notes |
|---|---|---|---|
| Variant relation manager | on `EcProductResource` | simple-resource (relation manager) | Matrix generator button + bulk-edit table (SKU, price override, stock, image). |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.variants.manage')
        && BillingService::hasModule('ecommerce.variants');
}
```

## Search & Realtime

None. Variants are surfaced through their parent product's storefront page.

## Jobs & Scheduling

None.
