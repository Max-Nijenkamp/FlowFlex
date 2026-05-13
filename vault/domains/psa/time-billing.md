---
type: module
domain: Professional Services (PSA)
panel: psa
module-key: psa.billing
status: planned
color: "#4ADE80"
---

# Time & Billing

> Billable time tracking against client engagements with invoice generation from approved time entries.

**Panel:** `psa`
**Module key:** `psa.billing`

---

## What It Does

Time & Billing closes the loop between delivery and revenue. Team members log time against specific projects and deliverables, optionally noting the type of work and whether it is billable. Managers review and approve time entries before they are included in a billing run. The module then generates invoices from approved billable entries, applying the correct rate card for each resource, and pushes the invoice into the Finance panel for payment tracking. Expenses can also be logged and billed alongside time.

---

## Features

### Core
- Time entry: log hours against a project and phase; mark as billable or non-billable
- Billable rate: pull rate from the resource's standard rate or a project-specific override
- Timesheet view: weekly timesheet grid for efficient bulk time entry
- Manager approval: submit weekly timesheets for manager review and approval
- Invoice generation: create an invoice from all approved billable entries for a client and period
- Expense logging: log reimbursable expenses against a project with receipt upload

### Advanced
- Rate card management: define multiple rates per resource (standard, senior, partner) and apply per project
- Billing milestones: fixed-fee billing triggers when a deliverable or milestone is accepted
- Draft invoice preview: review the invoice before sending with line-item breakdown
- Credit notes: issue credit notes for disputed billable hours
- Multi-currency billing: generate invoices in the client's preferred currency

### AI-Powered
- Time entry suggestions: AI suggests project and phase based on the team member's recent calendar activity
- Non-billable time alert: flag when non-billable time is rising disproportionately on a project
- Invoice anomaly detection: flag invoices that are significantly higher or lower than the project average

---

## Data Model

```erDiagram
    time_entries {
        ulid id PK
        ulid company_id FK
        ulid project_id FK
        ulid phase_id FK
        ulid resource_id FK
        date entry_date
        decimal hours
        boolean is_billable
        decimal rate
        text description
        string status
        ulid approved_by FK
        timestamp approved_at
        timestamps created_at_updated_at
    }

    psa_expenses {
        ulid id PK
        ulid project_id FK
        ulid resource_id FK
        ulid company_id FK
        date expense_date
        string category
        decimal amount
        string currency
        boolean is_billable
        string receipt_url
        string status
        timestamps created_at_updated_at
    }

    time_entries }o--|| psa_projects : "logged against"
    psa_expenses }o--|| psa_projects : "logged against"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `time_entries` | Time logs | `id`, `company_id`, `project_id`, `resource_id`, `hours`, `is_billable`, `rate`, `status` |
| `psa_expenses` | Expense logs | `id`, `project_id`, `resource_id`, `amount`, `is_billable`, `status` |

---

## Permissions

```
psa.billing.log-own-time
psa.billing.approve-timesheets
psa.billing.generate-invoices
psa.billing.view-all-time
psa.billing.manage-rates
```

---

## Filament

- **Resource:** `App\Filament\Psa\Resources\TimeEntryResource`
- **Pages:** `ListTimeEntries`, `CreateTimeEntry`
- **Custom pages:** `TimesheetPage`, `BillingRunPage`, `InvoicePreviewPage`
- **Widgets:** `BillableHoursWidget`, `PendingApprovalWidget`
- **Nav group:** Billing

---

## Displaces

| Feature | FlowFlex | Harvest | Toggl Track | Mavenlink |
|---|---|---|---|---|
| Billable time logging | Yes | Yes | Yes | Yes |
| Timesheet approval | Yes | Yes | No | Yes |
| Invoice generation | Yes | Yes | No | Yes |
| Milestone billing | Yes | No | No | Yes |
| Included in platform | Yes | No | No | No |

---

## Related

- [[project-delivery]] — time logged against specific deliverables
- [[resource-planning]] — rates sourced from resource records
- [[profitability]] — approved time entries feed cost calculations
- [[finance/INDEX]] — invoices pushed to finance for payment tracking
