---
type: module
domain: Customer Success
panel: cs
phase: 5
status: complete
cssclasses: domain-cs
migration_range: 970000–970999
last_updated: 2026-05-12
---

# Customer Health Scoring

Composite health score per customer account, computed from product usage, support ticket volume, NPS, billing health, and engagement signals. Powers CS alerting and at-risk detection.

---

## Health Score Components

Default scoring model (weights configurable per tenant):

| Signal | Weight | Source | Green | Amber | Red |
|---|---|---|---|---|---|
| Product usage | 30% | PLG events | > target | 50–80% target | < 50% target |
| Feature adoption | 20% | PLG feature definitions | ≥ 3 core features | 1–2 features | 0 features |
| Support tickets | 15% | Helpdesk / ITSM | 0 open critical | 1 open | 2+ open / CSAT < 3 |
| NPS score | 15% | PLG NPS surveys | 9–10 | 7–8 | 0–6 |
| Billing health | 10% | Finance / Subscriptions | No overdue | Payment plan | Dunning/overdue |
| Engagement | 10% | CS activities logged | QBR in 90 days | QBR > 90 days | No activity > 120 days |

**Composite score**: weighted average 0–100  
- 70–100: Green (healthy)  
- 40–69: Amber (at risk — monitor)  
- 0–39: Red (critical — immediate intervention)

---

## Score Calculation

Computed nightly via scheduled job:
1. Pull latest signal values from each source domain
2. Normalise each signal to 0–100 scale
3. Apply weights
4. Sum → composite health score
5. Compare to previous score → flag changes ≥ 10 points

Score history retained: can view 12-month health score trend per account.

---

## Custom Scoring Models

Tenants can define their own models:
- Add/remove signals
- Adjust weights
- Set thresholds (what counts as Green/Amber/Red per signal)
- Per product line (enterprise customers may have different health model than SMB)

---

## Health Dashboard

**Portfolio view**: all accounts in a grid — health score + trend arrow
- Filter: by CSM owner, segment, plan tier, renewal month
- Sort: by health score (ascending = most at-risk first)
- Bulk actions: assign CSM, create playbook run

**Account drill-down**: signal breakdown for one account
- Each signal shown with current value, trend, and contribution to composite score
- Last score change: when and by how much
- Open risks/playbooks linked

---

## Data Model

### `cs_health_scores`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| crm_company_id | ulid | FK |
| score | decimal(5,2) | 0–100 |
| tier | enum | green/amber/red |
| signals | json | {signal_key: {value, normalised, weighted}} |
| previous_score | decimal(5,2) | |
| score_change | decimal(5,2) | |
| computed_at | timestamp | |

---

## Migration

```
970000_create_cs_health_scores_table
970001_create_cs_health_score_models_table
970002_create_cs_health_score_signals_table
```

---

## Related

- [[MOC_CustomerSuccess]]
- [[cs-playbooks-alerts]] — triggers from score drops
- [[renewal-forecasting]] — score feeds renewal risk
- [[MOC_PLG]] — product usage and NPS source
- [[MOC_CRM]] — company record
