---
type: module
domain: Subscription Billing & RevOps
panel: subscriptions
phase: 4
status: complete
cssclasses: domain-subscriptions
migration_range: 975925–979999
last_updated: 2026-05-12
---

# Revenue Recognition (IFRS 15 / ASC 606)

Automate deferred revenue schedules, straight-line recognition of subscription fees, and compliance with IFRS 15 (International) and ASC 606 (US GAAP). Eliminates manual spreadsheet rev rec.

---

## The Problem

When a customer pays €1,200 for an annual subscription upfront:
- Cash received: €1,200 today
- Revenue earned: €100/month over 12 months

Without recognition schedules, booking the full €1,200 as revenue in month 1 is **non-compliant** with IFRS 15 / ASC 606 and will fail an audit.

---

## Recognition Methods

### Straight-Line (default for subscriptions)
Revenue recognised evenly over the service period:
```
€1,200 annual plan → €100/month × 12 months
```
Daily rate used when period doesn't align to months.

### Milestone-Based (for professional services)
Recognise revenue when performance obligations met:
- 50% on contract signature
- 50% on go-live delivery

Defined per product/plan. Links to [[MOC_PSA]] project milestones.

### Usage-Based
Recognise revenue as units consumed (for pure usage billing).

---

## Deferred Revenue Balance Sheet

- **Deferred Revenue** (liability): cash received but not yet earned
- **Recognised Revenue** (P&L): earned this period
- Monthly journal entries auto-generated:
  ```
  DR Deferred Revenue    100.00
    CR Revenue           100.00
  ```
- Pushed as draft journals to [[general-ledger-chart-of-accounts]]

---

## Compliance Reports

- Revenue waterfall: recognised vs deferred by period
- Rollforward: opening deferred + new bookings − recognitions = closing deferred
- Disclosure pack: IFRS 15 contract asset/liability disclosure (for annual accounts)

---

## Data Model

### `sub_recognition_schedules`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| subscription_id | ulid | FK |
| invoice_id | ulid | FK |
| total_amount | decimal(14,4) | |
| method | enum | straight_line/milestone/usage |
| start_date | date | |
| end_date | date | |

### `sub_recognition_entries`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| schedule_id | ulid | FK |
| period_start | date | |
| period_end | date | |
| amount | decimal(14,4) | |
| recognised_at | timestamp | nullable |
| gl_journal_id | ulid | nullable FK |

---

## Migration

```
975925_create_sub_recognition_schedules_table
975926_create_sub_recognition_entries_table
```

---

## Related

- [[MOC_SubscriptionBilling]]
- [[recurring-billing-engine]] — invoice triggers recognition schedule
- [[general-ledger-chart-of-accounts]] — journal entry destination
- [[MOC_FPA]] — recognised revenue feeds P&L forecast
