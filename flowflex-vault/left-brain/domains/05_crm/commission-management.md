---
type: module
domain: CRM & Sales
panel: crm
phase: 3
status: planned
cssclasses: domain-crm
migration_range: 307500–307999
last_updated: 2026-05-09
---

# Commission Management

Calculate, track, and pay sales commissions. Reps see their real-time earnings. Finance gets accurate accruals. Eliminates spreadsheet commission hell and removes disputes.

---

## Commission Plans

Configurable per role/team:

**Flat rate**: 5% of closed deal value

**Tiered**: accelerators for overachievement
| Quota attainment | Rate |
|---|---|
| 0–50% | 3% |
| 51–100% | 5% |
| 101–150% | 8% (accelerator) |
| 150%+ | 10% (kicker) |

**MRR-based**: monthly recurring revenue × multiplier
**Multi-element**: base + upsell + renewal components

---

## Splits and Overlays

Complex deals:
- AE + SDR split (e.g., 80% / 20%)
- Overlay roles: SE (solutions engineer) overlay % of deal value
- Manager override: partial credit for deal manager helped close
- All splits tracked and paid correctly

---

## Commission Timeline

```
Deal closed → Commission calculated → Pending (clawback window)
→ Payment period end → Commission approved → Paid via payroll
```

**Clawback**: if customer cancels within 90 days, commission reversed (configurable).

---

## Rep Earnings Dashboard

Live portal for each rep:
- YTD earnings vs OTE (on-target earnings)
- Current quarter: pending commissions on open deals
- Attainment: % of quota
- Expected payout next payment cycle

No more "when do I get paid for deal X?" queries to finance.

---

## Finance Integration

Monthly commission accrual journal auto-generated:
```
DR Sales Commission Expense    12,450.00
  CR Accrued Commissions        12,450.00
```
At payment: CR bank / DR accrued commissions.

---

## Data Model

### `crm_commission_plans`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| plan_type | enum | flat/tiered/mrr/multi |
| effective_from | date | |
| tiers | json | rate brackets |

### `crm_commission_entries`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| rep_id | ulid | FK |
| opportunity_id | ulid | FK |
| plan_id | ulid | FK |
| gross_amount | decimal(14,2) | |
| commission_amount | decimal(14,2) | |
| status | enum | pending/approved/paid/clawed_back |
| paid_at | timestamp | nullable |

---

## Migration

```
307500_create_crm_commission_plans_table
307501_create_crm_commission_entries_table
```

---

## Related

- [[MOC_CRM]]
- [[territory-quota-management]]
- [[sales-forecasting]]
- [[MOC_Finance]] — payroll + accruals
