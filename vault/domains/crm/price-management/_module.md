---
domain: crm
module: price-management
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# CRM Price Management

Product/service catalogue, price books, volume discounts, and CPQ (configure-price-quote). Absorbed from the former Pricing Management domain.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module Key

```
module-key:        crm.pricing
priority:          v1
panel:             crm
permission-prefix: crm.pricing
tables:            crm_products, crm_price_books, crm_price_book_entries, crm_volume_discounts
```

## Dependencies

| Kind | Module | Why |
|---|---|---|
| Hard | [[../deals/_module\|Deals]] | Products feed deal line items. |
| Hard | [[../../../infrastructure/module-catalog\|core.billing]] | Module gating (`hasModule`). |
| Hard | [[../../../security/authn-authz\|core.rbac]] | Permissions and role scoping. |
| Soft | [[../quotes/_module\|Quotes]] | CPQ resolution in the quote builder. |
| Soft | [[../segments/_module\|Segments]] | Price-book assignment per customer segment. |
| Soft | [[../../finance/multi-currency/_module\|finance.currency / multi-currency]] | Per-currency price books. |

## Core Features

- Product / service catalogue (name, SKU, description, unit, standard price).
- Price books (standard, partner, region-specific, customer-tier pricing).
- Volume discount rules (tiered pricing by quantity).
- Promotional pricing, time-bound discounts *(assumed: price book entry with `valid_from` / `valid_until`)*.
- Price book assignment per customer segment or account.
- CPQ: resolve price for (product, account, quantity, date) → quote/deal line.
- Margin guard: warn when a discount drives price below cost + threshold.
- Currency-aware pricing (links Multi-Currency).

## See features/

- [[features/cpq-resolution|CPQ price resolution]]
- [[features/volume-discounts|Volume discounts]]

## Build Manifest

```
database/migrations/xxxx_create_crm_products_table.php
database/migrations/xxxx_create_crm_price_books_table.php
database/migrations/xxxx_create_crm_price_book_entries_table.php
database/migrations/xxxx_create_crm_volume_discounts_table.php
app/Models/CRM/{Product,PriceBook,PriceBookEntry,VolumeDiscount}.php
app/Data/CRM/{CreateProductData,ResolvePriceData,PriceResolutionData}.php
app/Services/CRM/PricingService.php
app/Actions/CRM/AssignPriceBookAction.php
app/Filament/CRM/Resources/{ProductResource,PriceBookResource}.php
database/factories/CRM/{ProductFactory,PriceBookFactory}.php
tests/Feature/CRM/PriceResolutionTest.php
```

## Test Checklist

- [ ] Tenant isolation + module gating enforced.
- [ ] Price resolution order (account book > segment > default > standard) with fixtures.
- [ ] Volume tier picks the highest qualifying `min_quantity`.
- [ ] Promo entry applies only inside its validity window.
- [ ] Margin guard flags below-cost pricing.
- [ ] Duplicate SKU rejected.
- [ ] Exactly one default price book enforced.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Feeds | read API (`PricingService::resolve()`) | crm.quotes | Line-item price resolution in the quote builder. Read API, not events. |
| Feeds | read API (`PricingService::resolve()`) | crm.deals | Line-item price resolution on deal lines. Read API, not events. |
| Reads | read query | crm.segments | Segment → price-book assignment. Read-only *(assumed)*. |
| Reads | read query | finance.multi-currency | Per-currency price books. Read-only (soft dep). |
| Fires / Consumes | — | — | No cross-domain events fired or consumed — pricing is a pure read/resolve provider. |

**Data ownership:** `crm.pricing` writes only `crm_products`, `crm_price_books`, `crm_price_book_entries`, and `crm_volume_discounts`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../quotes/_module|Quotes]]
- [[../deals/_module|Deals]]
- [[../../finance/multi-currency/_module|Finance Multi-Currency]]
- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../../glossary]]
