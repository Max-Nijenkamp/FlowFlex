---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: complete
migration_range: 200005
last_updated: 2026-05-12
right_brain_log: "[[builder-log-finance-phase3]]"
---

# Client Billing & Retainers

Manage retainer contracts with clients and generate invoices for time-based or fixed-fee engagements. Phase 3: manually create invoices for retainer clients. Phase 6: auto-billing via scheduled jobs.

**Panel:** `finance`  
**Phase:** 3  
**Module key:** `finance.billing`

---

## Data Model

Uses the `invoices` table (see [[invoicing]]). Retainers are tracked as a lightweight overlay:

```erDiagram
    retainer_contracts {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        string title
        decimal monthly_amount
        string currency
        date start_date
        date end_date
        string billing_cycle
        string status
        string invoice_description
        ulid created_by FK
    }
```

**Retainer status:** `active` | `paused` | `cancelled` | `expired`

**Billing cycle:** `monthly` | `quarterly` | `annually`

---

## Features

### Retainer Contracts
- Create retainer agreement per client (contact) with fixed monthly amount
- Track start/end date and billing cycle
- Status management: activate, pause, cancel

### Invoice Generation (Phase 3 — Manual)
- "Generate invoice" button on retainer → pre-fills invoice with retainer amount + description
- Finance team manually triggers per billing cycle
- Phase 6: cron job auto-generates invoices on billing cycle date

### Time-Based Billing
- Pull unbilled time entries from Projects module (see [[MOC_Projects]])
- Summarise hours × rate per project per billing period
- Generate invoice from time entries (marks entries as billed)
- Phase 3: manual review and create; Phase 6: auto-draft on cycle date

---

## Permissions

```
finance.billing.view
finance.billing.create
finance.billing.manage-retainers
```

---

## Related

- [[MOC_Finance]]
- [[invoicing]] — retainer invoice creation
- [[MOC_Projects]] — time entries → billable hours
