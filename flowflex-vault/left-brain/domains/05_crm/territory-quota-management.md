---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 8
status: planned
migration_range: 250000–299999
last_updated: 2026-05-09
---

# Territory & Quota Management

Sales territory assignment, rep quota setting, attainment tracking, and commission calculations. Replaces Xactly, QuotaPath, and CaptivateIQ.

---

## Features

### Territory Management
- Territory hierarchy (Global → Region → Country → Territory)
- Assignment rules: by geography, industry, company size, account list
- Account ownership auto-assigned based on territory rules
- Territory conflict detection and resolution
- Territory splits (shared accounts between reps)
- Territory rebalancing tool (visualise coverage gaps)

### Quota Setting
- Annual/quarterly quota per rep, team, region
- Quota templates (% of team total, market-based, historical-based)
- Quota approval workflow (rep → manager → VP)
- Mid-year quota adjustments with reasoning log
- Ramp schedules for new hires (gradual quota build-up)

### Attainment Tracking
- Real-time quota attainment dashboard (rep and manager view)
- Pipeline coverage ratio (pipeline / quota)
- Forecast accuracy score
- At-risk reps flagged automatically (< 50% attainment at 60% through quarter)

### Commission Plans
- Tiered commission rates (accelerators above 100% quota)
- SPIFs (special performance incentives)
- Clawback rules (deal reversed within 90 days)
- Commission statement per pay period
- Commission posting to Payroll module

---

## Data Model

```erDiagram
    sales_territories {
        ulid id PK
        ulid company_id FK
        string name
        string parent_id FK
        json assignment_rules
    }

    sales_quotas {
        ulid id PK
        ulid user_id FK
        ulid territory_id FK
        integer period_year
        integer period_quarter
        decimal quota_amount
        string status
    }

    commission_plans {
        ulid id PK
        ulid company_id FK
        string name
        json tiers
        json spifs
        json clawback_rules
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `QuotaAttained` | Rep hits 100% | Notifications (rep + manager), HR (bonus trigger) |
| `CommissionEarned` | Deal closed + commission calculated | Finance (Payroll), Notifications (rep) |
| `TerritoryReassigned` | Account ownership changes | CRM (update contact owner), Notifications |

---

## Permissions

```
crm.territories.manage
crm.quotas.view
crm.quotas.manage
crm.commissions.view
crm.commissions.approve
```

---

## Competitors Displaced

Xactly · QuotaPath · CaptivateIQ · Performio · Spiff

---

## Related

- [[MOC_CRM]]
- [[entity-employee]]
- [[MOC_Finance]] — commission → payroll
