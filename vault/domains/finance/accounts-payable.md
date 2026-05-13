---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.ap
status: planned
color: "#4ADE80"
---

# Accounts Payable

> Supplier invoice management, payment scheduling, AP aging reports, and payment run creation — managing what the company owes to suppliers.

**Panel:** `finance`
**Module key:** `finance.ap`

## What It Does

Accounts Payable manages the lifecycle of supplier invoices — from receipt through approval to payment. Finance enters supplier bills, routes them for approval, and schedules payments. The AP aging report shows what is owed and when it is due. Payment runs group multiple supplier payments due in a period for batch processing. On posting a supplier bill, the GL entry (debit Expense, credit Accounts Payable) is created automatically. On recording payment, the GL entry (debit Accounts Payable, credit Bank) completes the cycle.

## Features

### Core
- Supplier bill entry: supplier name, invoice reference, date, due date, line items (description, amount, expense category, GL account), attachment (scanned invoice)
- Bill approval: submitted bill routes to Finance manager for approval — only approved bills can be scheduled for payment
- AP aging report: all outstanding supplier bills by aging bucket (current, 1–30, 31–60, 60+ days)
- Payment recording: mark a bill as paid on a specific date from a specific bank account — posts GL journal (debit AP, credit Bank)
- GL posting: on bill approval, auto-posts journal — debit Expense account, credit Accounts Payable

### Advanced
- Payment runs: select all bills due in the next 7 days → create a payment run → export to bank payment format (SEPA XML, BACS, or CSV) for upload to online banking
- Early payment discount: if supplier offers 2% discount for payment within 10 days, record the discount on payment and post discount income to GL
- Recurring bills: set a supplier bill to recur monthly (e.g. rent, SaaS subscription) — new bill auto-created on cadence
- Supplier credit notes: record a credit note from a supplier — reduces outstanding balance; posts reversal journal
- Three-way match: match supplier bill against purchase order and delivery receipt (requires Procurement module) before approving payment

### AI-Powered
- Bill classification: AI reads the uploaded scanned supplier invoice and suggests the expense category and GL account for each line item
- Duplicate detection: AI checks incoming bill reference and amount against existing bills — warns if this appears to be a duplicate submission

## Data Model

```erDiagram
    supplier_bills {
        ulid id PK
        ulid company_id FK
        ulid supplier_id FK
        string reference
        date bill_date
        date due_date
        string currency
        decimal subtotal
        decimal tax_total
        decimal total
        decimal amount_paid
        string status
        ulid approved_by FK
        timestamp approved_at
        ulid journal_entry_id FK
        timestamps created_at/updated_at
    }

    supplier_bill_lines {
        ulid id PK
        ulid bill_id FK
        string description
        decimal amount
        decimal tax_amount
        ulid account_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | draft / pending_approval / approved / scheduled / paid / void |
| `supplier_id` | FK to a suppliers table (name, contact, bank details) |
| `account_id` | GL expense account this line posts to |

## Permissions

- `finance.ap.view`
- `finance.ap.create-bill`
- `finance.ap.approve-bill`
- `finance.ap.create-payment-run`
- `finance.ap.record-payment`

## Filament

- **Resource:** `SupplierBillResource`, `SupplierResource`
- **Pages:** `ListSupplierBills`, `CreateSupplierBill`, `ViewSupplierBill`
- **Custom pages:** `ApAgingReportPage`, `PaymentRunPage`
- **Widgets:** `ApOutstandingWidget` — total outstanding AP balance on finance dashboard
- **Nav group:** Expenses (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero Bills | Supplier invoice and AP management |
| QuickBooks AP | Accounts payable |
| Tipalti | AP automation and payment |
| Bill.com | AP and supplier payment management |

## Related

- [[general-ledger]]
- [[expenses]]
- [[bank-accounts]]
- [[cash-flow]]
- [[tax-management]]
