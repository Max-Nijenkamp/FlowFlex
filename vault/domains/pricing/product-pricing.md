---
type: module
domain: Pricing Management
panel: pricing
module-key: pricing.products
status: planned
color: "#4ADE80"
---

# Product Pricing

> Product and SKU base prices — cost, margin, list price, and volume tier pricing within each price book.

**Panel:** `pricing`
**Module key:** `pricing.products`

---

## What It Does

Product Pricing manages the price of each product or SKU within a price book. Each product entry captures the cost price (sourced from the supplier catalogue or manual entry), the target margin, the resulting list price, and any volume tier breaks. Price managers can set prices manually or use formula-based pricing where list price is derived automatically from cost plus a margin percentage. Changes to product prices within a price book create an audit trail showing who changed what and when.

---

## Features

### Core
- Product price entry: product, price book, cost price, margin percentage, and list price
- Formula pricing: list price auto-calculated from cost + margin; manual override available
- Volume tiers: configure quantity break pricing (e.g. 1–9 units at list, 10–49 at 5% off, 50+ at 10% off)
- Price history: full audit trail of price changes per product per price book
- Bulk import: upload product prices via CSV for initial setup or large catalogue changes
- Price rounding: configure rounding rules per price book (e.g. round to nearest £0.05)

### Advanced
- Cost price sync: pull cost price from the procurement supplier catalogue automatically
- Margin floor: set a minimum margin percentage per product category; alert if a proposed price would breach it
- Price list export: export a formatted price list PDF for sending to customers
- Comparison view: compare prices for the same product across multiple price books side by side
- Effective date per price line: schedule a future price change to take effect on a specific date
- Unit of measure pricing: price the same product differently per UOM (e.g. per unit vs per case)

### AI-Powered
- Optimal price recommendation: suggest the price most likely to maximise margin while remaining competitive
- Margin erosion detection: flag products where the effective price (after discounts) is consistently below the margin floor
- Price sensitivity modelling: estimate the demand impact of a proposed price change based on historical quote conversion data

---

## Data Model

```erDiagram
    price_book_products {
        ulid id PK
        ulid company_id FK
        ulid price_book_id FK
        ulid product_id FK
        decimal cost_price
        decimal margin_percent
        decimal list_price
        boolean is_formula_based
        date effective_from
        timestamps created_at_updated_at
    }

    price_volume_tiers {
        ulid id PK
        ulid company_id FK
        ulid price_book_product_id FK
        integer min_quantity
        integer max_quantity
        decimal tier_price
        decimal discount_percent
        timestamps created_at_updated_at
    }

    price_book_products ||--o{ price_volume_tiers : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `price_book_products` | Product prices per book | `id`, `company_id`, `price_book_id`, `product_id`, `cost_price`, `margin_percent`, `list_price`, `effective_from` |
| `price_volume_tiers` | Volume break pricing | `id`, `price_book_product_id`, `min_quantity`, `max_quantity`, `tier_price`, `discount_percent` |

---

## Permissions

```
pricing.products.view
pricing.products.edit-prices
pricing.products.bulk-update
pricing.products.view-cost
pricing.products.export
```

---

## Filament

- **Resource:** `App\Filament\Pricing\Resources\PriceBookProductResource`
- **Pages:** `ListPriceBookProducts`, `CreatePriceBookProduct`, `EditPriceBookProduct`
- **Custom pages:** `PriceComparisonPage`, `MarginAnalysisPage`
- **Widgets:** `LowMarginProductsWidget`, `RecentPriceChangesWidget`
- **Nav group:** Price Books

---

## Displaces

| Feature | FlowFlex | Vendavo | PROS | Zilliant |
|---|---|---|---|---|
| Formula-based pricing | Yes | Yes | Yes | Yes |
| Volume tier pricing | Yes | Yes | Yes | Yes |
| Margin floor enforcement | Yes | Yes | Yes | Yes |
| AI optimal price recommendation | Yes | No | Partial | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[price-books]] — product prices belong to a price book
- [[discount-rules]] — discounts applied on top of product list prices
- [[procurement/supplier-catalog]] — cost prices sourced from supplier catalogue
- [[ecommerce/INDEX]] — list prices published to storefront product catalogue
