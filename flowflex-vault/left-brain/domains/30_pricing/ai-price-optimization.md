---
type: module
domain: Pricing Management
panel: pricing
module: AI Price Optimisation
phase: 5
status: planned
cssclasses: domain-pricing
migration_range: 1101000–1101499
last_updated: 2026-05-09
---

# AI Price Optimisation

ML-driven price recommendations using historical win/loss data, demand signals, competitor prices, and price elasticity modelling. Helps revenue teams set prices that maximise revenue, not just volume.

---

## Key Tables

```sql
CREATE TABLE pricing_optimisation_models (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100),
    model_type      ENUM('elasticity','win_probability','competitor_based','value_based'),
    status          ENUM('training','active','inactive','failed'),
    trained_at      TIMESTAMP NULL,
    accuracy_score  DECIMAL(5,4) NULL,
    config          JSON,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE pricing_price_recommendations (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    product_id      ULID NOT NULL,
    segment         VARCHAR(100) NULL,
    current_price   DECIMAL(12,2),
    recommended_price DECIMAL(12,2),
    confidence_score DECIMAL(5,4),
    rationale       JSON,     -- {win_rate_impact: +3%, revenue_impact: +€12k/mo}
    model_id        ULID NOT NULL REFERENCES pricing_optimisation_models(id),
    status          ENUM('pending_review','approved','rejected','applied'),
    reviewed_by     ULID NULL REFERENCES users(id),
    reviewed_at     TIMESTAMP NULL,
    generated_at    TIMESTAMP DEFAULT NOW()
);

CREATE TABLE pricing_ab_tests (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100),
    product_id      ULID NOT NULL,
    control_price   DECIMAL(12,2),
    variant_price   DECIMAL(12,2),
    traffic_split   TINYINT DEFAULT 50,  -- 50 = 50/50
    status          ENUM('draft','running','completed','cancelled'),
    started_at      TIMESTAMP NULL,
    ended_at        TIMESTAMP NULL,
    winner          ENUM('control','variant','inconclusive') NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## Model Training Data

Input features:
- Historical quote prices + won/lost status
- Deal size, customer segment, industry
- Seasonal signals (month, quarter)
- Product category, bundle composition
- Competitor prices (from [[competitor-price-monitoring]])
- Days to close (velocity signal)

Trained on minimum 200 deals per product/segment for reliability.

---

## Price Elasticity

Elasticity coefficient `e` = % change in demand / % change in price.  
`e < -1` → elastic (price-sensitive segment: be careful)  
`e > -1` → inelastic (low price sensitivity: margin opportunity)

Displayed in pricing dashboard as heatmap by product × segment.

---

## Recommendation Review

All AI recommendations require human review before applying.  
Pricing manager sees: recommended price, confidence %, projected revenue impact, comparables.  
One-click approve → price book updated, CRM quote templates synced.

---

## Related

- [[MOC_Pricing]]
- [[price-book-management]]
- [[competitor-price-monitoring]]
- [[pricing-analytics]]
