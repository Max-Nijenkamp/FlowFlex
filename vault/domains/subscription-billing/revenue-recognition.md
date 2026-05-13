---
type: module
domain: Subscription Billing & RevOps
panel: billing
module-key: billing.recognition
status: planned
color: "#4ADE80"
---

# Revenue Recognition

> ASC 606 / IFRS 15 compliant revenue recognition — deferred revenue scheduling, recognition triggers, and journal entry generation.

**Panel:** `billing`
**Module key:** `billing.recognition`

---

## What It Does

Revenue Recognition automates the accounting treatment of subscription and service revenue in compliance with ASC 606 (US GAAP) or IFRS 15. When an annual subscription invoice is paid, the revenue cannot all be recognised immediately — it must be recognised ratably over the service period (one-twelfth per month). The module generates a recognition schedule for every invoice, tracks the deferred revenue balance, and produces the monthly journal entries for recognised and deferred amounts that post to the Finance panel's general ledger.

---

## Features

### Core
- Recognition schedule: auto-generated per invoice based on the service period (billing interval and start/end dates)
- Ratable recognition: recognise revenue evenly over the service period (one-twelfth per month for annual subscriptions)
- Deferred revenue balance: current deferred revenue balance per account and in aggregate
- Monthly recognition run: trigger the monthly journal entry generation for all active schedules
- Journal entries: debit deferred revenue, credit recognised revenue — posted to Finance GL
- Recognition summary: total recognised, total deferred, and total invoiced for any period

### Advanced
- Point-in-time recognition: for one-off deliverables, recognise all revenue at the delivery date
- Multiple performance obligations: split a single invoice across multiple obligations with separate recognition schedules
- Contract modification handling: adjust recognition schedules when a subscription is upgraded, downgraded, or extended mid-term
- Catch-up entries: auto-calculate catch-up journals when a schedule is corrected retrospectively
- Audit trail: immutable log of every journal entry generated with source schedule and calculation basis

### AI-Powered
- Obligation identification: AI suggests how to split a contract across multiple performance obligations
- Recognition anomaly detection: flag recognition schedules where the total recognised exceeds total invoiced
- Disclosure drafting: AI drafts the revenue recognition accounting policy note from the configured settings

---

## Data Model

```erDiagram
    recognition_schedules {
        ulid id PK
        ulid invoice_id FK
        ulid account_id FK
        ulid company_id FK
        decimal total_amount
        date service_start
        date service_end
        string recognition_method
        decimal recognised_to_date
        decimal deferred_balance
        timestamps created_at_updated_at
    }

    recognition_schedule_lines {
        ulid id PK
        ulid schedule_id FK
        date recognition_date
        decimal amount
        boolean is_posted
        string journal_entry_reference
    }

    recognition_schedules ||--o{ recognition_schedule_lines : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `recognition_schedules` | Recognition schedules | `id`, `invoice_id`, `account_id`, `total_amount`, `service_start`, `service_end`, `recognised_to_date`, `deferred_balance` |
| `recognition_schedule_lines` | Monthly recognition entries | `id`, `schedule_id`, `recognition_date`, `amount`, `is_posted`, `journal_entry_reference` |

---

## Permissions

```
billing.recognition.view
billing.recognition.manage-schedules
billing.recognition.run-recognition
billing.recognition.export-journals
billing.recognition.view-audit-log
```

---

## Filament

- **Resource:** `App\Filament\Billing\Resources\RecognitionScheduleResource`
- **Pages:** `ListRecognitionSchedules`, `ViewRecognitionSchedule`
- **Custom pages:** `MonthlyRecognitionRunPage`, `DeferredRevenueBalancePage`, `JournalExportPage`
- **Widgets:** `DeferredRevenueWidget`, `RecognisedThisMonthWidget`, `ScheduleHealthWidget`
- **Nav group:** Revenue

---

## Displaces

| Feature | FlowFlex | Chargebee | Zuora | Custom spreadsheets |
|---|---|---|---|---|
| Automated recognition schedules | Yes | Yes | Yes | Manual |
| Multiple performance obligations | Yes | No | Yes | Manual |
| Journal entry generation | Yes | No | Yes | Manual |
| AI obligation identification | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[invoicing]] — invoices trigger recognition schedule creation
- [[mrr-analytics]] — recognised revenue feeds RevOps metrics
- [[finance/INDEX]] — journal entries posted to finance general ledger
