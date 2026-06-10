---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.pricing
status: planned
priority: v1
depends-on: [crm.deals, core.billing, core.rbac]
soft-depends: [crm.quotes, crm.segments, finance.currency]
fires-events: []
consumes-events: []
patterns: [money]
tables: [crm_products, crm_price_books, crm_price_book_entries, crm_volume_discounts]
permission-prefix: crm.pricing
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Price Management

Product/service catalogue, price books, volume discounts, and CPQ (configure-price-quote). Absorbed from the former Pricing Management domain.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/deals\|crm.deals]] | products feed deal line items |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/crm/quotes\|crm.quotes]] | CPQ in quote builder |
| Soft | [[domains/crm/customer-segments\|crm.segments]] | price book assignment per segment |
| Soft | [[domains/finance/multi-currency\|finance.currency]] | per-currency price books |

---

## Core Features

- Product/service catalogue: name, SKU, description, unit, standard price
- Price books: standard, partner, region-specific, customer-tier pricing
- Volume discount rules: tiered pricing by quantity
- Promotional pricing: time-bound discounts *(assumed: price book entry with valid_from/valid_until)*
- Price book assignment per customer segment or account
- CPQ: resolve price for (product, account, quantity, date) → quote/deal line
- Margin guard: warn when discount drives price below cost + threshold
- Currency-aware pricing (links Multi-Currency)

---

## Data Model

### crm_products

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| sku | string | unique `(company_id, sku)` |
| description | text nullable | |
| unit | string | piece / hour / month etc. |
| standard_price_cents / cost_cents | bigint | |
| is_active | boolean default true | |
| deleted_at | timestamp nullable | |

### crm_price_books

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | unique per company |
| currency | string(3) | |
| is_default | boolean | exactly one default per company |
| deleted_at | timestamp nullable | |

### crm_price_book_entries

| Column | Type | Notes |
|---|---|---|
| id, price_book_id FK, product_id FK, company_id | ulid | unique `(price_book_id, product_id, valid_from)` |
| price_cents | bigint | |
| valid_from / valid_until | date nullable | promo windows |

### crm_volume_discounts

| Column | Type | Notes |
|---|---|---|
| id, product_id FK, company_id | ulid | |
| min_quantity | decimal(10,2) | unique `(product_id, min_quantity)` |
| discount_percent | decimal(5,2) | |

---

## DTOs

### CreateProductData — name, sku (unique per company), unit, standard_price_cents (min:0), cost_cents (min:0)
### ResolvePriceData — product_id, account_id?, quantity, date? → PriceResolutionData (price_cents, source_book, volume_discount_applied, below_margin_warning)

## Services & Actions

- `PricingService::resolve(ResolvePriceData $data): PriceResolutionData` — order: account's book → segment book → default book → standard price; then volume tier; margin check against `cost_cents + threshold`
- `AssignPriceBookAction::run(string $bookId, string $accountOrSegmentId): void`

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ProductResource` | #1 CRUD resource | catalogue, active toggle |
| `PriceBookResource` | #1 CRUD resource | entries relation manager, promo windows |
| `VolumeDiscountResource` | #1 CRUD (or relation on product) | tiers |

---

## Permissions

`crm.pricing.view-any` · `crm.pricing.manage-products` · `crm.pricing.manage-price-books`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Price resolution order (account book > segment > default > standard) with fixtures
- [ ] Volume tier picks highest qualifying min_quantity
- [ ] Promo entry applies only inside validity window
- [ ] Margin guard flags below-cost pricing
- [ ] Duplicate SKU rejected
- [ ] Exactly one default price book enforced

---

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

---

## Related

- [[domains/crm/quotes]]
- [[domains/crm/deals]]
- [[domains/finance/multi-currency]]
