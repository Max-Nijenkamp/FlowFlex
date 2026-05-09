---
type: module
domain: Operations & Field Service
panel: operations
cssclasses: domain-operations
phase: 5
status: planned
migration_range: 300000–399999
last_updated: 2026-05-09
---

# Demand Planning & Inventory Forecasting

Statistical demand forecasting to predict future stock requirements. Different from MRP (which is requirement-driven from firm orders). Forecasting uses historical sales patterns, seasonality, trend analysis, and external signals to recommend purchase quantities before orders arrive.

**Panel:** `operations`  
**Phase:** 5 — builds on inventory history established in Phase 4

---

## Features

### Forecasting Models
- **Simple moving average** — average of last N periods
- **Weighted moving average** — recent periods weighted higher
- **Exponential smoothing** — Holt-Winters with trend and seasonality
- **Seasonal decomposition** — isolate trend + seasonal component + residual
- **AI-enhanced forecast** — ML model trained on full sales history per SKU per location
- Automatic model selection: system tests all models, picks best fit per SKU (lowest MAPE)

### Demand Signals
- Historical sales orders (primary signal)
- Open sales orders (firm demand)
- Marketing calendar (upcoming campaigns → demand spike)
- Promotions calendar (link to Promotions Engine → flag expected uplift)
- External events (import event calendar — Black Friday, seasonal peaks)
- Manual override: planner can adjust forecast by % for known events

### ABC-XYZ Analysis
- **ABC**: A = top 80% of revenue, B = next 15%, C = long tail
- **XYZ**: X = stable demand, Y = variable, Z = erratic/lumpy
- Combined AX items = high-value, stable = tightest control, most accurate forecast
- Combined CZ items = low-value, erratic = bulk order or discontinue

### Reorder Recommendations
- Calculate safety stock per SKU (based on demand variability + supplier lead time variability)
- Reorder point = average demand × lead time + safety stock
- Recommended order quantity (EOQ — Economic Order Quantity or MOQ from supplier)
- Compare recommendation vs current stock on hand + on order → buy if below reorder point
- One-click convert recommendation to draft purchase order

### Inventory Health Dashboard
- Overstock alerts (>X months of supply on hand)
- Understock alerts (below safety stock)
- Dead stock (no sales in 90+ days)
- Slow-moving items (sales rate declining)
- Stock-out history (% of days out of stock per SKU — measure of lost sales)

### Forecast Accuracy Tracking
- MAPE (Mean Absolute Percentage Error) per SKU per period
- Bias detection (consistently over or under forecasting)
- Forecast vs actual comparison chart
- Accuracy trend over time (is the model improving?)

---

## Data Model

```erDiagram
    demand_forecasts {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        ulid location_id FK
        date forecast_period
        decimal forecast_quantity
        string model_used
        decimal mape
        decimal confidence_lower
        decimal confidence_upper
        json demand_signals
        timestamp generated_at
    }

    inventory_health_metrics {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        date calculated_for
        string abc_class
        string xyz_class
        decimal months_of_supply
        decimal safety_stock_days
        decimal reorder_point
        decimal recommended_order_qty
        boolean is_overstock
        boolean is_understock
        boolean is_dead_stock
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `ReorderRecommendationGenerated` | Weekly forecast run | Operations (draft POs created), Notifications (purchasing manager) |
| `StockOutRiskDetected` | Forecast shows stock-out in <14 days | Notifications (urgent — purchasing manager) |
| `DeadStockDetected` | No movement in 90 days | Notifications (ops manager — liquidation decision) |

---

## Permissions

```
operations.forecasting.view
operations.forecasting.run
operations.forecasting.override
operations.forecasting.convert-to-po
```

---

## Competitors Displaced

Inventory Planner · Lokad · Streamline · Netstock · EazyStock · Slimstock

---

## Related

- [[MOC_Operations]]
- [[entity-product]]
- [[manufacturing-bom]] — MRP uses firm orders; forecasting uses statistical demand
- [[warehouse-management]]
