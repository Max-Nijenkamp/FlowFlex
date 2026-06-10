---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.invoicing
status: planned
priority: v1-core
depends-on: [finance.ledger, core.billing, core.rbac, core.settings, foundation.queues]
soft-depends: [crm.deals, crm.contacts, finance.tax, finance.ar, finance.currency]
fires-events: [InvoicePaid]
consumes-events: [DealWon]
patterns: [states, service, money, pdf, email, events]
tables: [fin_invoices, fin_invoice_lines, fin_payments, fin_customers]
permission-prefix: finance.invoicing
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Invoicing

Customer invoice creation, sending, payment tracking, and recurring invoice automation. Core revenue tracking for every SME.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/general-ledger\|finance.ledger]] | payments post journal entries |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/company-settings\|core.settings]] | gating, permissions, currency/branding on PDFs |
| Hard | [[domains/foundation/queue-workers\|foundation.queues]] | PDF + mail + recurring jobs |
| Soft | [[domains/crm/deals\|crm.deals]] | consumes `DealWon` → draft invoice stub |
| Soft | [[domains/crm/contacts\|crm.contacts]] | customer linked to CRM account; standalone `fin_customers` otherwise |
| Soft | [[domains/finance/tax-management\|finance.tax]] | configured tax rates per line; without it: single default rate from settings *(assumed)* |
| Soft | [[domains/finance/accounts-receivable\|finance.ar]] | aging/dunning on top |
| Soft | [[domains/finance/multi-currency\|finance.currency]] | foreign-currency invoices; base currency only without it |

---

## Core Features

- Invoice creation: customer, line items (description, qty, unit price, tax rate), discount, notes
- Invoice status lifecycle: `draft → sent → partially_paid → paid → overdue | voided` (spatie/laravel-model-states)
- PDF generation via `spatie/laravel-pdf` (Chromium-based, CSS-rendered) and email delivery to customer
- Payment recording: mark as paid (manual), partial payments supported
- Recurring invoices: schedule (monthly/quarterly/annually), auto-generate and send
- Invoice numbering: configurable prefix, auto-increment per company (e.g. INV-2026-001)
- Late payment: overdue detection, reminder email on configurable days-past-due
- Tax calculation: applied per line item from configured tax rates (brick/money, line-level rounding *(assumed: round per line, sum lines)*)
- Integration with CRM: `DealWon` creates draft invoice stub per event-bus contract
- Export invoice list via `pxlrbt/filament-excel`

---

## Data Model

### fin_customers *(new vs v1 spec — invoice recipient record, links to CRM when active)*

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| email | string | invoice delivery |
| address | jsonb | billing address |
| vat_number | string nullable | |
| crm_account_id | ulid nullable | link when CRM active |
| payment_terms_days | int default from settings *(assumed 14)* | |
| deleted_at | timestamp nullable | |

### fin_invoices

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| customer_id | ulid | not null FK fin_customers | |
| invoice_number | string | not null, unique `(company_id, invoice_number)` | sequential per company |
| status | string | default `draft` | state machine |
| issue_date / due_date | date | due ≥ issue | |
| subtotal_cents / tax_total_cents / total_cents / paid_amount_cents | bigint | not null default 0 | |
| currency | string(3) | not null | base unless multi-currency |
| exchange_rate | decimal(12,6) | nullable | multi-currency only |
| discount_percent | decimal(5,2) | default 0 | |
| notes | text | nullable | |
| recurring_schedule | string | nullable | monthly / quarterly / annually |
| next_recurring_at | date | nullable | |
| source_deal_id | ulid | nullable | DealWon origin |
| pdf_path | string | nullable | tenant-scoped |
| deleted_at | timestamp | nullable | kept 7y per [[architecture/data-lifecycle]] |

**Indexes:** `(company_id, status)`, `(company_id, due_date)`, `(company_id, customer_id)`

### fin_invoice_lines

| Column | Type | Notes |
|---|---|---|
| id, invoice_id FK, company_id | ulid | |
| description | string | |
| quantity | decimal(10,2) | min 0.01 |
| unit_price_cents | bigint | |
| tax_rate_id | ulid nullable | finance.tax |
| tax_cents / line_total_cents | bigint | computed |

### fin_payments

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), invoice_id FK | ulid | |
| amount_cents | bigint | > 0, ≤ remaining balance |
| payment_date | date | |
| payment_method | string | bank-transfer / stripe / cash / other |
| reference_number | string nullable | |

---

## State Machine

Column: `fin_invoices.status` — `InvoiceState`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `sent` | `finance.invoicing.send` | number assigned (if not yet), PDF generated, mail queued |
| `sent` | `partially_paid` | payment recorded < balance | journal entry posted |
| `sent` / `partially_paid` | `paid` | payment completes balance | fires `InvoicePaid`; journal entry |
| `sent` / `partially_paid` | `overdue` | scheduled command past due_date | reminder mail per config |
| `overdue` | `paid` / `partially_paid` | payment | as above |
| `draft` / `sent` / `overdue` | `voided` | `finance.invoicing.void` | reversal entry if anything posted; paid invoices cannot be voided |

Invoice numbers assigned at first send, never reused, gap-free per company *(assumed: advisory lock)*. Audited.

---

## DTOs

### CreateInvoiceData
| Field | Type | Validation |
|---|---|---|
| customer_id | string | required, ulid in company |
| issue_date / due_date | CarbonImmutable | required; due ≥ issue |
| lines | array<{description, quantity, unit_price_cents, tax_rate_id?}> | min:1; quantity ≥ 0.01; unit_price ≥ 0 |
| discount_percent | float | between:0,100 |
| notes | ?string | max:2000 |
| recurring_schedule | ?string | in:monthly,quarterly,annually |

### RecordPaymentData
| Field | Type | Validation |
|---|---|---|
| invoice_id | string | required |
| amount_cents | int | min:1; ≤ open balance ("Payment exceeds the open balance.") |
| payment_date | CarbonImmutable | required |
| payment_method | string | in set |
| reference_number | ?string | max:100 |

### InvoiceData (output) — id, invoice_number, customer_name, status, issue_date, due_date, subtotal_cents, tax_total_cents, total_cents, paid_amount_cents, balance_cents, currency, total_formatted, lines[], payments[]

## Services & Actions

Interface→Service: `InvoiceServiceInterface` → `InvoiceService`.

- `create(CreateInvoiceData $data): InvoiceData` — totals computed via brick/money
- `send(string $invoiceId): InvoiceData` — number + PDF + queued mail
- `recordPayment(RecordPaymentData $data): InvoiceData` — state move + `LedgerService::post` (AR ↓ / cash ↑); fires `InvoicePaid` when balance 0
- `void(string $invoiceId, string $reason): InvoiceData` — throws `CannotVoidPaidInvoiceException`
- Actions: `RecalculateInvoiceTotals`, `DuplicateInvoiceAction`

## Events

### Fires: InvoicePaid
| Payload field | Type |
|---|---|
| company_id | string |
| invoice_id | string |
| crm_account_id | ?string |
| amount_cents | int |
| currency | string |
| paid_at | CarbonImmutable |

### Consumes: DealWon (from crm.deals)
Listener: `CreateInvoiceStubListener` — draft invoice, lines from `crm_deal_products` (fallback single line "Deal: {name}"), due = customer payment terms, never auto-sent; no-op when module inactive. Contract: [[architecture/event-bus]].

---

## Filament

**Nav group:** Invoicing

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `InvoiceResource` | #1 CRUD resource | filters: status, date range; line-item repeater |
| Invoice view page | #2 detail | PDF preview + payment history + actions (Send, Record Payment, Void) |
| `CustomerResource` | #1 CRUD resource | billing customers |
| `InvoiceStatsWidget` | #6 widget | outstanding, overdue count, paid this month |

---

## Permissions

`finance.invoicing.view-any` · `finance.invoicing.view` · `finance.invoicing.create` · `finance.invoicing.update` · `finance.invoicing.send` · `finance.invoicing.record-payment` · `finance.invoicing.void` · `finance.invoicing.manage-customers`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `GenerateRecurringInvoicesCommand` | finance | daily 05:00 | WHERE `next_recurring_at <= today`; advances date in same transaction |
| `MarkOverdueInvoicesCommand` | finance | daily 06:30 | WHERE `status=sent AND due_date < today` — re-run marks nothing twice |
| `SendPaymentReminderCommand` | notifications | daily | per-bucket once flags *(AR module refines)* |
| `GenerateInvoicePdfJob` | exports | on send | overwrites pdf_path |
| `InvoiceMail` | notifications | on send | — |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Totals: lines + tax + discount exact via brick/money (incl. rounding fixture)
- [ ] Send assigns gap-free sequential number under concurrency
- [ ] Partial payment → partially_paid; completing payment fires `InvoicePaid` with contract payload
- [ ] Overpayment rejected
- [ ] Payment posts balanced journal entry
- [ ] Void of paid invoice rejected; void of sent posts reversal
- [ ] `DealWon` creates draft stub (lines copied, never sent); inactive module = no-op
- [ ] Recurring command idempotent (run twice = one invoice per schedule)
- [ ] Overdue detection only touches sent invoices past due

---

## Build Manifest

```
database/migrations/xxxx_create_fin_customers_table.php
database/migrations/xxxx_create_fin_invoices_table.php
database/migrations/xxxx_create_fin_invoice_lines_table.php
database/migrations/xxxx_create_fin_payments_table.php
app/Models/Finance/{Customer,Invoice,InvoiceLine,Payment}.php
app/States/Finance/Invoice/{InvoiceState,Draft,Sent,PartiallyPaid,Paid,Overdue,Voided}.php
app/Data/Finance/{CreateInvoiceData,RecordPaymentData,InvoiceData}.php
app/Contracts/Finance/InvoiceServiceInterface.php
app/Services/Finance/InvoiceService.php
app/Exceptions/Finance/CannotVoidPaidInvoiceException.php
app/Events/Finance/InvoicePaid.php
app/Listeners/Finance/CreateInvoiceStubListener.php
app/Actions/Finance/{RecalculateInvoiceTotals,DuplicateInvoiceAction}.php
app/Jobs/Finance/GenerateInvoicePdfJob.php
app/Mail/Finance/{InvoiceMail,PaymentReminderMail}.php
app/Console/Commands/Finance/{GenerateRecurringInvoicesCommand,MarkOverdueInvoicesCommand,SendPaymentReminderCommand}.php
app/Filament/Finance/Resources/{InvoiceResource,CustomerResource}.php
app/Filament/Finance/Widgets/InvoiceStatsWidget.php
database/factories/Finance/{CustomerFactory,InvoiceFactory,PaymentFactory}.php
tests/Feature/Finance/{InvoiceLifecycleTest,InvoiceTotalsTest,InvoicePaymentTest,DealWonStubTest,RecurringInvoiceTest}.php
```

---

## Related

- [[domains/finance/accounts-receivable]]
- [[domains/finance/general-ledger]]
- [[domains/crm/deals]]
- [[domains/finance/tax-management]]
- [[architecture/event-bus]]
