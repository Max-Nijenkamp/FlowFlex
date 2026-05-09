---
type: module
domain: Professional Services Automation
panel: psa
cssclasses: domain-psa
phase: 7
status: planned
migration_range: 878000–880999
last_updated: 2026-05-09
---

# Retainer & SOW Management

Manage monthly retainer buckets, track hour burndown, enforce rollover rules, and alert account managers when retainer hours approach zero.

---

## Core Functionality

### Retainer Definition
A retainer is a recurring engagement contract:
- Monthly or quarterly hour bucket (e.g., 40 hours/month)
- Start date, end date (or rolling)
- Rollover policy: expire unused hours / carry forward (capped or uncapped) / bank into paid-down bucket
- Retainer rate: fixed monthly fee or hours × rate
- Buffer threshold: alert at X% remaining (default 20%)

### Hour Burndown Tracking
- Time entries from Projects module auto-post against the active retainer period
- Live view: hours used / hours remaining / % burned
- Day-rate normalisation: if retainer is fixed fee, track equivalent days burned
- Overage tracking: hours logged beyond bucket → billable overage at agreed overage rate

### Period Management
- Monthly auto-roll to new period (creates new bucket, applies rollover policy)
- Manual period adjustment (if client needed an extension)
- Period lock: once invoiced, hours are locked (no retroactive edits)

### Rollover Rules
| Policy | Behaviour |
|---|---|
| **Expire** | Unused hours lost at period end — client cannot carry forward |
| **Carry forward (capped)** | Unused hours carry into next period, up to 1× monthly bucket max |
| **Carry forward (uncapped)** | All unused hours accumulate (rare, creates balance sheet liability) |
| **Credit** | Unused hours convert to credit at agreed rate — applied to future invoices |

---

## SOW Tracker

For fixed-price or milestone engagements (not retainers):

- Deliverables list from SOW
- % complete per deliverable (updated by PM)
- Milestone schedule: milestone name, agreed date, fee amount, invoicing trigger
- Milestone status: pending / completed / invoiced / overdue

Auto-invoicing trigger: when milestone marked complete → creates draft invoice in Finance AR.

---

## Client Retainer Portal

Optional client-facing view (via portal module):
- Current period: hours used, hours remaining, expiry date
- Last 6 months: burndown history, any overages
- Upcoming deliverables/milestones (for fixed projects)

Gives client transparency → reduces "where are my hours?" support queries.

---

## Data Model

### `psa_retainers`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| engagement_id | ulid | FK `psa_engagements` |
| monthly_hours | decimal(7,2) | |
| monthly_fee | decimal(14,2) | |
| currency | char(3) | |
| rollover_policy | enum | expire/carry_capped/carry_uncapped/credit |
| rollover_cap_hours | decimal(7,2) | nullable |
| overage_rate | decimal(10,2) | nullable, hourly rate for overages |
| alert_threshold_pct | int | default 20 |
| start_date | date | |
| end_date | date | nullable |

### `psa_retainer_periods`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| retainer_id | ulid | FK |
| period_start | date | |
| period_end | date | |
| allocated_hours | decimal(7,2) | bucket + rollover |
| logged_hours | decimal(7,2) | from time entries |
| overage_hours | decimal(7,2) | computed |
| rolled_over_hours | decimal(7,2) | carried to next period |
| locked | bool | true once invoiced |

---

## Notifications

| Trigger | Recipient |
|---|---|
| Retainer at 80% burned | Account manager |
| Retainer at 100% (overage zone) | Account manager + client contact |
| 3 days until period end with > 30% remaining | Account manager (possible scope conversation) |
| Milestone completion confirmed | Finance (trigger invoice) |

---

## Migration

```
878000_create_psa_retainers_table
878001_create_psa_retainer_periods_table
878002_create_psa_sow_milestones_table
```

---

## Related

- [[MOC_PSA]]
- [[client-engagement-management]]
- [[agency-billing-intelligence]]
- [[MOC_Finance]] — milestone billing → invoicing
