---
type: module
domain: Pricing Management
panel: pricing
module-key: pricing.competitive
status: planned
color: "#4ADE80"
---

# Competitive Pricing

> Competitor price tracking, gap analysis, and AI-powered repricing signals for key products and segments.

**Panel:** `pricing`
**Module key:** `pricing.competitive`

---

## What It Does

Competitive Pricing helps pricing and revenue operations teams understand how FlowFlex's prices compare to the market. Competitors are registered and their prices are tracked manually or via feed import for key products. The module calculates the price gap (the difference between FlowFlex's price and the lowest competitor price) per product and surfaces this alongside the current margin to give pricing managers the information they need to make repricing decisions. AI-powered analysis highlights which products are most price-sensitive and where a price reduction is likely to improve win rate.

---

## Features

### Core
- Competitor register: name, website, market segment, and primary geography
- Competitor product mapping: map competitor SKUs to FlowFlex products for apples-to-apples comparison
- Competitor price entry: log a competitor's price for a mapped product with source and date
- Price gap calculation: show the difference between FlowFlex list price and lowest competitor price per product
- Price positioning summary: view all tracked products with own price, lowest competitor price, and gap
- Price update history: audit trail of competitor price changes per product

### Advanced
- Bulk price import: import competitor price lists from CSV or structured feed
- Segment-level analysis: filter competitive analysis by customer segment, region, or product category
- Price index: calculate a composite price index showing overall market position (above/at/below market)
- Win/loss correlation: overlay win/loss rates from CRM on the competitive gap chart to identify price-sensitive products
- Alert on competitor price drop: notify the pricing team when a tracked competitor drops price below FlowFlex's price
- Source tracking: record the source of each competitor price (website scrape, customer report, deal loss note)

### AI-Powered
- Repricing recommendations: suggest price adjustments for products where competitive gap is causing deal losses
- Price sensitivity ranking: rank products by how much win rate varies with price relative to competition
- Market movement prediction: forecast likely competitor pricing moves based on their historical adjustment patterns

---

## Data Model

```erDiagram
    competitors {
        ulid id PK
        ulid company_id FK
        string name
        string website
        string market_segment
        string geography
        timestamps created_at_updated_at
    }

    competitor_product_mappings {
        ulid id PK
        ulid company_id FK
        ulid competitor_id FK
        ulid product_id FK
        string competitor_sku
        string competitor_product_name
        timestamps created_at_updated_at
    }

    competitor_prices {
        ulid id PK
        ulid company_id FK
        ulid mapping_id FK
        decimal price
        string currency
        string source
        date price_date
        timestamps created_at_updated_at
    }

    competitors ||--o{ competitor_product_mappings : "maps"
    competitor_product_mappings ||--o{ competitor_prices : "tracks"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `competitors` | Competitor records | `id`, `company_id`, `name`, `market_segment`, `geography` |
| `competitor_product_mappings` | FlowFlex-to-competitor SKU map | `id`, `competitor_id`, `product_id`, `competitor_sku` |
| `competitor_prices` | Tracked competitor prices | `id`, `mapping_id`, `price`, `currency`, `source`, `price_date` |

---

## Permissions

```
pricing.competitive.view
pricing.competitive.manage-competitors
pricing.competitive.enter-prices
pricing.competitive.view-analysis
pricing.competitive.export
```

---

## Filament

- **Resource:** `App\Filament\Pricing\Resources\CompetitorResource`
- **Pages:** `ListCompetitors`, `CreateCompetitor`, `EditCompetitor`, `ViewCompetitor`
- **Custom pages:** `PriceGapAnalysisPage`, `CompetitiveDashboardPage`, `RepricingSignalsPage`
- **Widgets:** `PricePositionIndexWidget`, `BelowMarketProductsWidget`
- **Nav group:** Intelligence

---

## Displaces

| Feature | FlowFlex | Vendavo | PROS | Zilliant |
|---|---|---|---|---|
| Competitor price tracking | Yes | Yes | Partial | Yes |
| Price gap analysis | Yes | Yes | Yes | Yes |
| Win/loss correlation | Yes | Partial | No | No |
| AI repricing recommendations | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[price-books]] — repricing signals inform price book updates
- [[product-pricing]] — competitive gaps compared against margin to find repricing room
- [[discount-rules]] — competitive intelligence informs minimum discount needed per deal
- [[crm/INDEX]] — win/loss data from CRM deals feeds competitive analysis
