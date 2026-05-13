---
type: module
domain: Customer Success
panel: cs
phase: 6
status: complete
cssclasses: domain-cs
migration_range: 970850–970999
last_updated: 2026-05-12
---

# Expansion Revenue Tracking

Identify and track upsell/cross-sell opportunities within the existing customer base. Expansion ARR analytics: net revenue retention, expansion vs contraction vs churn.

---

## Expansion Opportunities

CSMs identify expansion opportunities from signals:
- **Usage limit approach**: account approaching seat/usage limit → upsell to higher tier
- **Feature gap**: customer requesting feature in paid-only tier
- **New department**: customer expanding usage to new team
- **Module cross-sell**: customer on HR + Finance, not yet on Projects → cross-sell
- **Geographic expansion**: customer expanding to new countries → multi-entity module

Opportunities link to CRM opportunities (AE + CSM can co-own expansion).

---

## NRR (Net Revenue Retention)

Key SaaS investor metric: tracks how much revenue you retain and grow from existing customers.

```
NRR = (Starting ARR + Expansion - Contraction - Churn) / Starting ARR × 100
```

- NRR > 100%: growing even with churn (best-in-class: 120%+)
- NRR = 100%: exactly replacing what you lose
- NRR < 100%: shrinking

Dashboard shows:
- Current NRR (trailing 12 months)
- MRR movements waterfall: new + expansion − contraction − churn = net change
- Cohort expansion: how much does a Jan 2025 cohort spend after 6/12/24 months?

---

## Data Model

### `cs_expansion_opportunities`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| crm_company_id | ulid | FK |
| csm_id | ulid | FK `employees` |
| crm_opportunity_id | ulid | nullable FK |
| type | enum | upsell/cross_sell/expansion/new_module |
| description | varchar(500) | |
| potential_arr | decimal(14,2) | |
| stage | enum | identified/qualified/proposed/committed/won/lost |
| target_close_date | date | nullable |
| won_at | date | nullable |
| won_arr | decimal(14,2) | nullable |

---

## Migration

```
970850_create_cs_expansion_opportunities_table
970851_create_cs_arr_movement_snapshots_table
```

---

## Related

- [[MOC_CustomerSuccess]]
- [[renewal-forecasting]] — expansion at renewal
- [[customer-health-scoring]] — usage signals drive expansion
- [[MOC_CRM]] — expansion = CRM opportunity
- [[MOC_SubscriptionBilling]] — won expansion → subscription upgrade
