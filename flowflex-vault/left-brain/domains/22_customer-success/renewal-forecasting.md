---
type: module
domain: Customer Success
panel: cs
phase: 5
status: complete
cssclasses: domain-cs
migration_range: 970500–970699
last_updated: 2026-05-12
---

# Renewal Forecasting

Renewal pipeline management, churn risk flagging, renewal probability scoring, and CSM-level renewal forecast. Bridges CS and Finance for ARR retention visibility.

---

## Renewal Pipeline

All contracts/subscriptions with upcoming renewal dates tracked in a pipeline view:

Columns: Customer → ARR → Renewal Date → Health Score → Renewal Probability → Stage → CSM → Actions

### Renewal Stages
```
Too Early → 6+ months out, monitor health
Approaching → 3–6 months, begin renewal conversation
In Conversation → Active renewal discussion in progress
At Risk → Churn signals present, escalation required
Committed → Customer confirmed renewal verbally
Contracted → Renewal signed
Churned → Customer did not renew
```

### Renewal Probability Score (0–100%)
Computed from:
- Health score (heaviest weight)
- Days until renewal (urgency factor)
- Renewal stage
- Historical: did similar accounts with this health renew?
- Manual override by CSM (with required reason)

---

## ARR at Risk

Finance-facing view:
- Total ARR renewing in next 30/60/90 days
- ARR by renewal probability bucket: Likely (≥75%) / At Risk (40–74%) / Unlikely (<40%)
- Expected ARR retention = Σ (ARR × probability)
- YoY comparison

CFO can see: gross renewal rate, net revenue retention, logo retention.

---

## Churn Reasons

When a customer churns, record reason:
- Budgetary constraints
- Went to competitor (which competitor?)
- Product gaps (which features missing?)
- Company went out of business
- Merged/acquired
- Low usage / never adopted
- CS/support failure

Aggregate: top churn reasons by quarter → product and CS strategy input.

---

## Data Model

### `cs_renewals`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| crm_company_id | ulid | FK |
| csm_id | ulid | FK `employees` |
| arr | decimal(14,2) | |
| currency | char(3) | |
| renewal_date | date | |
| stage | enum | too_early/approaching/in_conversation/at_risk/committed/contracted/churned |
| renewal_probability | int | 0–100 |
| probability_override | bool | |
| probability_override_reason | text | nullable |
| churn_reason | varchar(100) | nullable |
| churn_reason_detail | text | nullable |
| renewed_at | date | nullable |
| renewed_arr | decimal(14,2) | nullable |

---

## Migration

```
970500_create_cs_renewals_table
970501_create_cs_renewal_activities_table
```

---

## Related

- [[MOC_CustomerSuccess]]
- [[customer-health-scoring]] — primary input to probability
- [[expansion-revenue-tracking]] — upsell at renewal
- [[MOC_Finance]] — renewed ARR → revenue recognition
- [[MOC_SubscriptionBilling]] — subscription renewal trigger
