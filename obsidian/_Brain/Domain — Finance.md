---
tags: [brain, domain, finance]
last_updated: 2026-05-07
---

# Domain — Finance

**Spec:** `04 - Finance/` — Invoicing, Expense Management, Financial Reporting  
**All models:** `app/Models/Finance/`  
**All resources:** `app/Filament/Finance/Resources/`  
**Panel:** `/finance` — guard `tenant`, color emerald `#059669`  
**Module key:** `finance`

All models carry: `BelongsToCompany`, `HasUlids`, `SoftDeletes`, `LogsActivity`.

---

## Models

### Invoice
**Spec:** `04 - Finance/Invoicing.md`  
**Table:** `invoices`  
**Purpose:** The primary billing document. Issued to a CRM contact. Has line items, payment records, email event tracking, and can be linked to a recurring template.

**Fillable fields:**
- `company_id`, `contact_id` (→ CrmContact — cross-domain link)
- `number` (string — human-readable invoice number, e.g. "INV-0042")
- `currency` (ISO 4217), `issue_date` (date), `due_date` (date)
- `status` → `InvoiceStatus` enum
- `discount_type` (string: percentage/fixed, nullable), `discount_value` (decimal, nullable)
- `tax_rate` (decimal)
- `subtotal` (decimal), `tax_amount` (decimal), `total` (decimal), `paid_amount` (decimal)
- `is_recurring` (bool), `recurring_invoice_id` (nullable — if generated from a RecurringInvoice)
- `notes` (text, nullable), `footer_text` (text, nullable)

**Casts:** `status` → `InvoiceStatus`, `issue_date`/`due_date` → date, `is_recurring` → boolean, all amounts → decimal

**Relations:**
- `lines()` → HasMany `InvoiceLine`
- `payments()` → HasMany `InvoicePayment`
- `creditNote()` → HasOne `CreditNote`
- `emailEvents()` → HasMany `InvoiceEmailEvent`
- `recurringInvoice()` → BelongsTo `RecurringInvoice` (nullable)
- `contact()` → BelongsTo `CrmContact` via `contact_id` (**cross-domain: Finance → CRM**)

**Status workflow:** Draft → Sent → PartiallyPaid → Paid → Overdue → WrittenOff  
**Activity log:** only `status`, `total`, `paid_amount`, `due_date` — never full line amounts  
**Events fired:** `InvoiceCreated`, `InvoiceSent`, `InvoicePaid`, `InvoiceOverdue`

---

### InvoiceLine
**Table:** `invoice_lines`  
**Purpose:** One line item on an invoice. Quantity × unit price minus discount plus tax.

**Fillable fields:**
- `invoice_id`, `description`
- `quantity` (decimal), `unit_price` (decimal)
- `discount` (decimal, nullable), `tax_rate` (decimal, nullable)
- `total` (decimal — computed: qty × unit_price - discount)

**Relations:**
- `invoice()` → BelongsTo `Invoice`

---

### InvoicePayment
**Table:** `invoice_payments`  
**Purpose:** Records a payment received against an invoice. Multiple partial payments are supported. Summed to set `Invoice.paid_amount`.

**Fillable fields:**
- `invoice_id`, `amount` (decimal), `payment_date` (date), `method` (string: bank_transfer/card/cheque/cash), `reference` (string, nullable), `notes` (text, nullable)

**Relations:**
- `invoice()` → BelongsTo `Invoice`

---

### InvoiceEmailEvent
**Table:** `invoice_email_events`  
**Purpose:** Audit log of email delivery events for invoices. Used to track whether the customer opened or clicked the invoice email.

**Fillable fields:**
- `invoice_id`
- `event_type` (string: sent/opened/bounced/clicked)
- `occurred_at` (datetime)
- `metadata` (JSON — e.g., user agent, IP on open event)

**Relations:**
- `invoice()` → BelongsTo `Invoice`

---

### CreditNote
**Table:** `credit_notes`  
**Purpose:** A document that reverses or reduces the amount owed on an invoice. Linked 1:1 to an invoice.

**Fillable fields:**
- `company_id`, `invoice_id`, `number` (string), `issue_date` (date)
- `amount` (decimal), `reason` (text)
- `status` (string: draft/issued/applied)

**Relations:**
- `invoice()` → BelongsTo `Invoice`

**N+1 prevention:** `CreditNoteResource::getEloquentQuery()` eager-loads `invoice` — the table displays `invoice.number`.

---

### RecurringInvoice
**Table:** `recurring_invoices`  
**Purpose:** Template that generates invoices on a schedule. `next_run_at` drives the scheduler. When it fires, a new Invoice is created from `template_data`.

**Fillable fields:**
- `company_id`, `contact_id` (→ CrmContact)
- `frequency` (string: weekly/monthly/quarterly/annually)
- `next_run_at` (date — when the next invoice will be generated)
- `last_run_at` (date, nullable — when last invoice was generated)
- `is_active` (bool)
- `template_data` (array/JSON — invoice configuration to clone: lines, terms, notes)

**Casts:** `next_run_at`/`last_run_at` → date, `is_active` → boolean, `template_data` → array

**Relations:**
- `invoices()` → HasMany `Invoice` (all invoices generated from this template)
- `contact()` → BelongsTo `CrmContact`

**Permissions:** `finance.recurring-invoices.view/create/edit/delete`

---

### Expense
**Spec:** `04 - Finance/Expense Management.md`  
**Table:** `expenses`  
**Purpose:** A single business expense submitted by a team member for reimbursement. Can be part of an expense report.

**Fillable fields:**
- `company_id`, `tenant_id` (submitter → Tenant), `expense_report_id` (nullable → ExpenseReport)
- `expense_category_id`, `description`
- `amount` (decimal), `currency`
- `expense_date` (date)
- `status` → `ExpenseStatus` enum (Pending/Approved/Rejected/Reimbursed)
- `rejection_reason` (text, nullable)
- `receipt_file_id` (nullable → File — scanned receipt)
- `vendor` (string, nullable), `mileage_km` (decimal, nullable)
- `approved_by` (tenant_id, nullable), `approved_at` (datetime, nullable)

**Casts:** `status` → `ExpenseStatus`, `expense_date` → date

**Relations:**
- `tenant()` → BelongsTo `Tenant` (submitter — scope dropdown to `company_id`)
- `expenseReport()` → BelongsTo `ExpenseReport`
- `expenseCategory()` → BelongsTo `ExpenseCategory`
- `receipt()` → BelongsTo `File` (via `receipt_file_id`)
- `approver()` → BelongsTo `Tenant` (via `approved_by`)

**Tenant dropdown security:** Expense form has two tenant dropdowns (`tenant_id`, `approved_by`) — both must scope to `company_id`. Tenant has no BelongsToCompany global scope.  
**Events fired:** `ExpenseSubmitted`, `ExpenseApproved`, `ExpenseRejected`

---

### ExpenseReport
**Table:** `expense_reports`  
**Purpose:** Groups multiple expenses into a batch for single approval. A team member submits all their monthly expenses as one report.

**Fillable fields:**
- `company_id`, `tenant_id` (submitter → Tenant)
- `title` (string), `status` (string: draft/submitted/approved/rejected)
- `submitted_at` (datetime, nullable)

**Casts:** `submitted_at` → datetime

**Relations:**
- `tenant()` → BelongsTo `Tenant`
- `expenses()` → HasMany `Expense`

**Permissions:** `finance.expense-reports.view/create/edit/delete/approve`

---

### ExpenseCategory
**Table:** `expense_categories`  
**Purpose:** Classification for expenses. Finance teams use `gl_code` to map to their accounting system (Xero, QuickBooks, Sage).

**Fillable fields:**
- `company_id`, `name`, `description` (nullable)
- `gl_code` (string, nullable — General Ledger account code for accounting integration)
- `is_active` (bool)

---

### MileageRate
**Table:** `mileage_rates`  
**Purpose:** Company-defined reimbursement rate per km per vehicle type. Used to auto-calculate mileage expense amounts.

**Fillable fields:**
- `company_id`, `rate_per_km` (decimal), `currency`
- `effective_date` (date), `vehicle_type` (string: car/van/motorcycle/cycle)
- `is_active` (bool)

---

## Resources (Finance Panel)

| Resource | Model | Nav Group | Permissions | Key Features |
|---|---|---|---|---|
| `InvoiceResource` | `Invoice` | Invoices | `finance.invoices.*` | Full CRUD, status transitions, getEloquentQuery override |
| `CreditNoteResource` | `CreditNote` | Invoices | `finance.credit-notes.*` | CRUD, eager-loads `invoice` (N+1 fix) |
| `RecurringInvoiceResource` | `RecurringInvoice` | Invoices | `finance.recurring-invoices.*` | CRUD |
| `ExpenseResource` | `Expense` | Expenses | `finance.expenses.*` | CRUD, both tenant dropdowns scoped to company_id |
| `ExpenseCategoryResource` | `ExpenseCategory` | Expenses | `finance.expense-categories.*` | CRUD |
| `MileageRateResource` | `MileageRate` | Expenses | `finance.mileage-rates.*` | CRUD |
| `ExpenseReportResource` | `ExpenseReport` | Reports | `finance.expense-reports.*` | CRUD, approve action |

**Financial Reporting:** Implemented as a custom Filament page `FinancialReporting` + `FinancialSummaryWidget`. Shows P&L summary, revenue, expenses, outstanding invoices. Permission: `finance.reports.view`.

---

## Enums

### InvoiceStatus
`App\Enums\Finance\InvoiceStatus`  
`Draft`, `Sent`, `PartiallyPaid`, `Paid`, `Overdue`, `WrittenOff`  
Colors: Draft=gray, Sent=info, PartiallyPaid=warning, Paid=success, Overdue=danger, WrittenOff=gray

### ExpenseStatus
`App\Enums\Finance\ExpenseStatus`  
`Pending`, `Approved`, `Rejected`, `Reimbursed`

---

## Events (Phase 3)

All wired in `EventServiceProvider`. All listeners implement `ShouldQueue`.

| Event | Listener | Type | What it does |
|---|---|---|---|
| `InvoiceCreated` | stub | stub | — |
| `InvoiceSent` | stub | stub | — |
| `InvoicePaid` | `NotifyAccountsTeam` | real | Internal notification |
| `InvoiceOverdue` | `SendOverdueReminder` | real | Email to contact |
| `ExpenseSubmitted` | `NotifyExpenseApprover` | real | Notification to approver |
| `ExpenseApproved` | `NotifyExpenseSubmitter` | real | Notification to submitter |
| `ExpenseRejected` | stub | stub | — |
| `CreditNoteIssued` | stub | stub | — |

---

## Cross-Domain Notes

- `Invoice.contact_id` → `CrmContact.id` — Finance invoices are always linked to a CRM contact
- `Expense.tenant_id` → `Tenant.id` — submitter is a workspace team member, not a CRM contact
- `ExpenseReport.tenant_id` → `Tenant.id` — same
- File receipts: `Expense.receipt_file_id` → `File` — always access via `FileStorageService`, never expose path
