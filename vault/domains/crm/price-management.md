---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.pricing
status: planned
color: "#4ADE80"
---

# Price Management

Product/service catalogue, price books, volume discounts, and CPQ (configure-price-quote). Absorbed from the former Pricing Management domain.

## Core Features

- Product/service catalogue: name, SKU, description, unit, standard price
- Price books: standard, partner, region-specific, customer-tier pricing
- Volume discount rules: tiered pricing by quantity
- Promotional pricing: time-bound discounts
- Price book assignment per customer segment or account
- CPQ: configure product options → calculate price → generate quote
- Margin guard: warn when discount exceeds threshold
- Currency-aware pricing (links Multi-Currency)

## Data Model

| Table | Key Columns |
|---|---|
| `crm_products` | company_id, name, sku, description, unit, standard_price_cents, cost_cents |
| `crm_price_books` | company_id, name, currency, is_default |
| `crm_price_book_entries` | price_book_id, product_id, company_id, price_cents |
| `crm_volume_discounts` | product_id, company_id, min_quantity, discount_percent |

## Filament

**Nav group:** Settings

- `ProductResource` — catalogue management
- `PriceBookResource` — price books + entries
- Used by Quotes (CPQ) and Deals (line items)

## Cross-Domain

- Products used in [[domains/crm/deals]] line items and [[domains/crm/quotes]]
- Uses `brick/money`

## Related

- [[domains/crm/quotes]]
- [[domains/crm/deals]]
- [[domains/finance/multi-currency]]
