---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 6
status: planned
migration_range: 200000–249999
last_updated: 2026-05-09
---

# Accounts Receivable Automation

AI-powered AR management — automated dunning sequences, payment prediction, dispute management, and collector workqueue. Replaces Tesorio, Chaser, and Gaviti.

---

## Features

### Automated Dunning
- Configurable multi-step dunning sequences (email + SMS + phone task)
- Sequences pause when payment received (real-time detection)
- Personalised messaging (AI writes first-line from invoice history)
- Escalation rules (e.g. after 30 days → legal template)
- Per-customer dunning preferences (VIP = softer tone)
- Bulk sequence enrolment

### Payment Prediction
- AI model: predicts which invoices will be paid late based on customer history
- Risk score per customer (green/amber/red)
- Cash flow forecast updated in real-time
- "Expected collection date" per invoice

### Dispute Management
- Dispute flag on invoice (reason codes)
- Dispute → internal task to account manager
- Dispute resolution workflow → partial credit or revised invoice
- Track dispute rate per customer

### Collector Workqueue
- Daily prioritised task list for AR team
- Call script suggestions per account
- One-click send reminder
- Log every contact attempt with outcome

### Reporting
- DSO (Days Sales Outstanding) trend
- Aging bucket analysis with drill-down
- Collection effectiveness index
- Bad debt provision recommendations

---

## Data Model

```erDiagram
    ar_dunning_sequences {
        ulid id PK
        ulid company_id FK
        string name
        json steps
        boolean is_default
    }

    ar_dunning_enrolments {
        ulid id PK
        ulid invoice_id FK
        ulid sequence_id FK
        integer current_step
        string status
        timestamp next_action_at
    }

    ar_disputes {
        ulid id PK
        ulid invoice_id FK
        string reason_code
        text description
        string status
        ulid assigned_to FK
        timestamp resolved_at
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `DunningEmailSent` | Step executed | Analytics (track campaign) |
| `DisputeRaised` | Customer disputes invoice | CRM (create task for AM), Notifications |
| `InvoicePaidAfterDunning` | Payment received | Analytics (measure dunning effectiveness) |

---

## Permissions

```
finance.ar.view-any
finance.ar.manage-sequences
finance.ar.manage-disputes
finance.ar.collector-workqueue
```

---

## Competitors Displaced

Tesorio · Chaser · Gaviti · YayPay · Invoiced AR

---

## Related

- [[MOC_Finance]]
- [[entity-invoice]]
- [[entity-contact]]
