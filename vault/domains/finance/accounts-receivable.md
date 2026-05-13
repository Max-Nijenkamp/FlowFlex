---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.ar
status: planned
color: "#4ADE80"
---

# Accounts Receivable

> Customer payment tracking, aging reports, dunning sequences, and payment reconciliation — turning outstanding invoices into collected cash.

**Panel:** `finance`
**Module key:** `finance.ar`

## What It Does

Accounts Receivable is the customer collections management layer built on top of Invoicing. While Invoicing handles creating and sending invoices, AR focuses on what happens after — tracking which customers owe money, how overdue each invoice is, and automating the follow-up process. The aging report categorises outstanding invoices into buckets (current, 1–30 days overdue, 31–60 days, 60+ days). Dunning sequences send automated payment reminder emails at configurable intervals. Payment allocation matches incoming payments to outstanding invoices and posts the GL entries.

## Features

### Core
- AR aging report: all outstanding invoices grouped by aging bucket (current, 1–30, 31–60, 61–90, 90+ days overdue) — total per bucket per customer
- Payment recording: record a payment against one or more outstanding invoices — partial payments supported
- GL posting: on payment recording, posts journal — debit Bank, credit Accounts Receivable
- Customer statement: PDF statement of all outstanding invoices for a customer — sent by email on demand
- Overdue dashboard: list of all overdue invoices sorted by days overdue with contact info and last activity

### Advanced
- Dunning sequences: configurable email templates sent at D+7, D+14, D+30 post due date — escalating tone (gentle reminder → firm reminder → final notice)
- Dunning exclusions: per-customer opt-out from automated dunning — handled manually by account manager
- Write-off: write off a bad debt — posts journal (debit Bad Debt Expense, credit Accounts Receivable) and marks invoice void
- Payment allocations: one payment can be split across multiple invoices — excess becomes a credit balance applied to next invoice
- Credit limit: configurable per customer — new invoices blocked when customer's outstanding balance exceeds their credit limit

### AI-Powered
- Payment risk scoring: AI scores each customer from 1–10 on payment risk based on payment history — high-risk customers highlighted in the AR dashboard
- Collection priority: AI ranks the top 10 invoices to prioritise for collection based on amount, days overdue, and customer risk score

## Data Model

```erDiagram
    payments {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        date payment_date
        decimal amount
        string currency
        string method
        string reference
        ulid bank_account_id FK
        ulid journal_entry_id FK
        timestamps created_at/updated_at
    }

    payment_allocations {
        ulid id PK
        ulid payment_id FK
        ulid invoice_id FK
        decimal allocated_amount
        timestamps created_at/updated_at
    }

    dunning_sequences {
        ulid id PK
        ulid company_id FK
        string name
        json steps
        boolean is_default
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `method` | bank_transfer / card / cheque / cash / other |
| `dunning_sequences.steps` | JSON array of {days_overdue, email_template} objects |

## Permissions

- `finance.ar.view`
- `finance.ar.record-payment`
- `finance.ar.manage-dunning`
- `finance.ar.write-off`
- `finance.ar.export-aging`

## Filament

- **Resource:** `PaymentResource`
- **Pages:** `ListPayments`, `RecordPayment`
- **Custom pages:** `ArAgingReportPage`, `ArDashboardPage`
- **Widgets:** `ArAgingBucketsWidget` — total outstanding per aging bucket on finance dashboard
- **Nav group:** Invoicing (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero AR | Accounts receivable management |
| QuickBooks AR | Customer payment tracking |
| Chaser | AR automation and dunning |
| YayPay | AR collections management |

## Related

- [[invoicing]]
- [[general-ledger]]
- [[bank-accounts]]
- [[cash-flow]]
- [[multi-currency]]
