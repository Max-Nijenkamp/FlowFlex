---
type: module
domain: Pricing Management
panel: pricing
module: Pricing Analytics
phase: 5
status: complete
cssclasses: domain-pricing
migration_range: 1102000–1102499
last_updated: 2026-05-12
---

# Pricing Analytics

Margin waterfall analysis, discount depth reporting, win/loss by price point, and deal velocity by price band. Answers the question: "Is our pricing strategy working?"

---

## Key Metrics

| Metric | Definition |
|---|---|
| Average Selling Price (ASP) | AVG(deal_value / qty) per product/period |
| Average Discount % | AVG(discount_pct) on closed-won deals |
| Discount Leakage (€) | List price total − actual revenue |
| Win Rate by Price Band | % won for deals at <5%, 5–15%, >15% discount |
| Gross Margin by Product | (Revenue − COGS) / Revenue |
| Margin by Sales Rep | Avg margin % per rep — identifies margin killers |
| Price Realisation Rate | Actual revenue / list price revenue = 1 − discount rate |

---

## Margin Waterfall

Visual breakdown from list price → net revenue:

```
List Price          €10,000
  − Volume discount    €500
  − Promo discount     €300
  − Rep discount       €200  (required approval)
  − Payment terms      €100  (2/10 net 30)
─────────────────────────────
Net Revenue         €8,900  (89% price realisation)
  − COGS             €4,500
─────────────────────────────
Gross Margin        €4,400  (49.4%)
```

---

## Key Tables

```sql
CREATE TABLE pricing_deal_snapshots (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    deal_id         ULID NOT NULL,          -- CRM opportunity/deal
    product_id      ULID NOT NULL,
    list_price      DECIMAL(12,2),
    net_price       DECIMAL(12,2),
    discount_pct    DECIMAL(5,2),
    discount_amount DECIMAL(12,2),
    quantity        DECIMAL(8,2),
    deal_value      DECIMAL(12,2),
    cogs            DECIMAL(12,2) NULL,
    margin_pct      DECIMAL(5,2) NULL,
    outcome         ENUM('won','lost','no_decision') NULL,
    loss_reason     VARCHAR(100) NULL,
    price_book_id   ULID NULL,
    rep_id          ULID NULL REFERENCES users(id),
    closed_at       DATE NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## Win/Loss by Price Point

Chart: x-axis = discount band, y-axis = win rate.  
Reveals: "Our win rate collapses above 20% discount — customers at that level aren't price-sensitive, they want features we don't have."  
Or: "Win rate peaks at 8–12% discount. Reps giving away more margin than needed."

---

## Related

- [[MOC_Pricing]]
- [[price-book-management]]
- [[discount-approval-workflows]]
- [[ai-price-optimization]]
- [[MOC_Analytics]]
