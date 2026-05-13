---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.analytics
status: planned
color: "#4ADE80"
---

# Analytics

> Revenue, conversion rate, average order value, customer lifetime value, and channel performance in a read-only ecommerce dashboard.

**Panel:** `ecommerce`
**Module key:** `ecommerce.analytics`

## What It Does

Ecommerce Analytics is the read-only reporting layer for the ecommerce panel. It aggregates data from orders, payments, customers, products, and channels into a set of pre-built dashboards and KPI cards that answer the most common ecommerce questions: How much did we sell today? What is our conversion rate? Which products drive the most revenue? Which channel has the best customer LTV? Data refreshes daily and cannot be modified from this module.

## Features

### Core
- Revenue dashboard: total revenue, orders, average order value (AOV), gross margin — by day, week, month, and year; comparison to prior period
- Conversion funnel: sessions → product views → add to cart → checkout started → order placed; drop-off rate at each step
- Top products: ranked by revenue, units sold, and return rate
- Customer metrics: new vs returning customer ratio, repeat purchase rate, average purchase frequency
- Channel performance: revenue, orders, AOV, and conversion rate per channel (own storefront, Amazon, eBay, social)
- Geographic breakdown: revenue by country, region, and city

### Advanced
- Customer lifetime value (CLV): average CLV by acquisition channel and first-purchase cohort
- Cohort retention: track repeat purchase rate for customers acquired each month over 12 months
- Product performance matrix: revenue vs margin heatmap per product category
- Abandoned cart funnel: abandonment rate at each checkout step; recovery rate from abandoned cart emails
- Promotion ROI: revenue during vs outside promotion periods; net margin after discount
- Seasonal trends: year-over-year comparison of weekly revenue to identify growth and seasonality patterns

### AI-Powered
- Weekly insight digest: AI-generated summary of performance vs prior week with notable changes highlighted
- Churn prediction: identify customers at risk of lapsing based on recency, frequency, and monetary (RFM) scoring

## Data Model

```erDiagram
    ec_analytics_daily {
        ulid id PK
        ulid company_id FK
        string channel
        date report_date
        decimal revenue
        integer orders
        decimal aov
        integer sessions
        integer add_to_carts
        integer checkouts_started
        decimal conversion_rate
        timestamps timestamps
    }

    ec_customer_metrics {
        ulid id PK
        ulid company_id FK
        ulid customer_id FK
        integer order_count
        decimal total_spend
        decimal average_order_value
        date first_order_date
        date last_order_date
        decimal predicted_ltv
        string rfm_segment
        timestamps timestamps
    }

    ec_analytics_daily }o--|| ec_customer_metrics : "aggregated from"
```

| Table | Purpose |
|---|---|
| `ec_analytics_daily` | Daily channel-level aggregated ecommerce metrics |
| `ec_customer_metrics` | Per-customer LTV and RFM scoring |

## Permissions

```
ecommerce.analytics.view-any
ecommerce.analytics.export
ecommerce.analytics.view-customer-ltv
ecommerce.analytics.view-channel-breakdown
ecommerce.analytics.manage-goals
```

## Filament

**Resource class:** none (read-only pages only)
**Pages:** none
**Custom pages:** `EcommerceRevenueDashboardPage`, `ConversionFunnelPage`, `ProductPerformancePage`, `CustomerCohortPage`
**Widgets:** `RevenueTodayWidget`, `ConversionRateWidget`, `AovWidget`, `TopProductsWidget`
**Nav group:** Analytics

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Shopify Analytics | Native ecommerce performance reporting |
| Google Analytics 4 (ecommerce) | Conversion funnel and product reporting |
| Triple Whale | Ecommerce attribution and LTV analysis |
| Glew.io | Ecommerce analytics and customer segmentation |

## Related

- [[orders]] — order data feeds all revenue metrics
- [[abandoned-carts]] — abandonment funnel data sourced here
- [[multi-channel]] — channel breakdown uses channel data
- [[recommendations]] — recommendation revenue attribution tracked here
- [[../analytics/dashboards]] — ecommerce KPIs surfaced in company-wide BI dashboards
