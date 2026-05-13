---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: complete
migration_range: 200013
last_updated: 2026-05-12
right_brain_log: "[[builder-log-finance-phase3]]"
---

# Subscription & MRR Tracking

Track recurring revenue metrics (MRR, ARR, churn) for the company's own customer subscriptions. Manual entry in Phase 3. Replaces Maxio (ChargeBee), ProfitWell, Baremetrics.

**Panel:** `finance`  
**Phase:** 3  
**Module key:** `finance.subscriptions`

Note: This tracks the FlowFlex *customer's* subscription revenue — not FlowFlex's own billing. For FlowFlex's billing, see the Admin panel.

---

## Data Model

```erDiagram
    subscriptions {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        string plan_name
        decimal mrr_amount
        string currency
        string status
        date started_at
        date cancelled_at
        string cancellation_reason
        date trial_ends_at
        timestamp last_updated
    }
```

**Subscription status:** `trial` | `active` | `paused` | `cancelled` | `expired`

---

## Features

### MRR Dashboard
- Total MRR: sum of all active subscription `mrr_amount` values
- ARR: MRR × 12
- MRR movement: New MRR, Expansion MRR, Contraction MRR, Churned MRR, Net New MRR
- Month-over-month chart

### Subscription Management
- Manual entry: add/update subscriptions with plan name, MRR, and status
- Record churn: mark subscription cancelled with reason (price, competitor, product, etc.)
- Trial tracking: trial start/end, conversion to paid

### Churn Analysis
- Churn rate: cancelled MRR / beginning MRR × 100
- Customer churn count vs revenue churn
- Churn reasons breakdown (pie chart)
- Cohort retention: % of customers from a given month still active

---

## Permissions

```
finance.subscriptions.view
finance.subscriptions.manage
finance.subscriptions.export
```

---

## Related

- [[MOC_Finance]]
- [[invoicing]] — subscription invoice generation
- [[financial-reporting]] — MRR as a revenue line in P&L
