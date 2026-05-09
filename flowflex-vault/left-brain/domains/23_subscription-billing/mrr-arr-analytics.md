---
type: module
domain: Subscription Billing & RevOps
panel: subscriptions
phase: 3
status: planned
cssclasses: domain-subscriptions
migration_range: 975700–975849
last_updated: 2026-05-09
---

# MRR / ARR Analytics

Investor-grade SaaS revenue metrics: MRR movements, ARR, churn rates, cohort analysis, and net revenue retention. The financial heartbeat of a SaaS business.

---

## Core Metrics

### MRR (Monthly Recurring Revenue)
```
MRR = Σ (subscription monthly equivalent value)
Annual plan €1,200 → contributes €100 MRR
Monthly plan €99 → contributes €99 MRR
```

### MRR Movement Waterfall
Each month broken into:
| Movement | Definition |
|---|---|
| New MRR | MRR from brand-new customers |
| Expansion MRR | MRR increase from existing customers (upgrades, seats) |
| Contraction MRR | MRR decrease from downgrades |
| Churn MRR | MRR lost from cancellations |
| Reactivation MRR | MRR from previously churned customers who re-subscribed |
| **Net New MRR** | New + Expansion − Contraction − Churn + Reactivation |

### ARR (Annual Recurring Revenue)
`ARR = MRR × 12`

### Churn Rates
- **Logo churn**: % of customers who cancelled (count-based)
- **Revenue churn (Gross)**: % of MRR lost from cancellations
- **Revenue churn (Net)**: % of MRR lost after expansion offsets (NRR)

---

## Cohort Analysis

Tracks what percentage of a starting cohort's revenue remains after N months:

| Cohort | Month 1 | Month 3 | Month 6 | Month 12 |
|---|---|---|---|---|
| Jan 2025 | 100% | 88% | 82% | 76% |
| Feb 2025 | 100% | 91% | 87% | 81% |

Good benchmarks: Month 12 retention > 75% for SMB SaaS.

---

## Dashboard Widgets

- MRR trend (12-month chart, with waterfall breakdown)
- ARR (large number, YoY growth %)
- Monthly logo churn rate
- Net Revenue Retention (NRR %)
- Quick Ratio: (New + Expansion) / (Contraction + Churn) — should be ≥ 4
- LTV:CAC ratio (requires marketing spend data)

---

## Data Model

### `sub_mrr_snapshots`
Monthly computed per subscription:

| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| month | date | First day of month |
| subscription_id | ulid | FK |
| crm_company_id | ulid | FK |
| mrr | decimal(14,4) | |
| movement_type | enum | new/expansion/contraction/churn/reactivation/existing |
| plan_id | ulid | FK |
| currency | char(3) | |

---

## Migration

```
975700_create_sub_mrr_snapshots_table
975701_create_sub_cohort_snapshots_table
```

---

## Related

- [[MOC_SubscriptionBilling]]
- [[subscription-lifecycle-management]] — source data
- [[revenue-recognition]] — recognized revenue ≠ MRR
- [[MOC_CustomerSuccess]] — NRR feeds CS strategy
- [[MOC_FPA]] — ARR feeds FP&A forecasting
