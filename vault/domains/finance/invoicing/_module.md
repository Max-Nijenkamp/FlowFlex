---
domain: finance
module: invoicing
type: module
module-key: finance.invoicing
priority: v1-core
build-status: planned
status: wip
depends-on: [finance.ledger, core.billing, core.rbac, core.settings, foundation.queues]
soft-depends: [crm.deals, crm.contacts, finance.tax, finance.ar, finance.currency]
fires-events: [InvoicePaid]
consumes-events: [DealWon]
patterns: [states, service, money, pdf, email, events]
tables: [fin_invoices, fin_invoice_lines, fin_payments, fin_customers]
permission-prefix: finance.invoicing
encrypted-fields: []
color: "#4ADE80"
updated: 2026-06-20
---

# Invoicing

Customer invoice creation, sending, payment tracking, and recurring invoice automation. Core revenue tracking for every SME.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Purpose

Invoicing is the revenue front door. Invoices are created, sent as PDFs, and tracked through payment. Every recorded payment is intended to post a balanced journal entry through `LedgerService::post` (AR ↓ / cash ↑) — invoicing never writes ledger truth directly. Recurring schedules and overdue detection run as scheduled commands.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | payments post journal entries |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/company-settings/_module\|core.settings]] | gating, permissions, currency/branding on PDFs |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | PDF + mail + recurring jobs |
| Soft | [[../../crm/deals/_module\|crm.deals]] | consumes `DealWon` → draft invoice stub |
| Soft | [[../../crm/contacts/_module\|crm.contacts]] | customer linked to CRM account; standalone `fin_customers` otherwise |
| Soft | [[../tax-management/_module\|finance.tax]] | configured tax rates per line; without it a single default rate from settings *(assumed)* |
| Soft | [[../accounts-receivable/_module\|finance.ar]] | aging/dunning on top |
| Soft | [[../multi-currency/_module\|finance.currency]] | foreign-currency invoices; base currency only without it |

## Core Features

- Invoice creation: customer, line items (description, qty, unit price, tax rate), discount, notes.
- Invoice status lifecycle: `draft → sent → partially_paid → paid → overdue | voided` (spatie/laravel-model-states) — see [[features/invoice-lifecycle]].
- PDF generation via `spatie/laravel-pdf` (Chromium-based, CSS-rendered) and email delivery to customer.
- Payment recording: mark as paid (manual), partial payments supported — see [[features/payments]].
- Recurring invoices: schedule (monthly/quarterly/annually), auto-generate and send — see [[features/recurring-invoices]].
- Invoice numbering: configurable prefix, auto-increment per company (e.g. `INV-2026-001`).
- Late payment: overdue detection, reminder email on configurable days-past-due.
- Tax calculation: applied per line item from configured tax rates (brick/money, line-level rounding *(assumed: round per line, sum lines)*).
- Integration with CRM: `DealWon` creates a draft invoice stub per event-bus contract.
- Export invoice list via `pxlrbt/filament-excel`.

## Permissions

`finance.invoicing.view-any` · `finance.invoicing.view` · `finance.invoicing.create` · `finance.invoicing.update` · `finance.invoicing.send` · `finance.invoicing.record-payment` · `finance.invoicing.void` · `finance.invoicing.manage-customers`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `GenerateRecurringInvoicesCommand` | finance | daily 05:00 | WHERE `next_recurring_at <= today`; advances date in same transaction |
| `MarkOverdueInvoicesCommand` | finance | daily 06:30 | WHERE `status=sent AND due_date < today` — re-run marks nothing twice |
| `SendPaymentReminderCommand` | notifications | daily | per-bucket once flags *(AR module refines)* |
| `GenerateInvoicePdfJob` | exports | on send | overwrites `pdf_path` |
| `InvoiceMail` | notifications | on send | — |

See [[../../../architecture/queue-jobs]].

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Totals: lines + tax + discount exact via brick/money (incl. rounding fixture)
- [ ] Send assigns gap-free sequential number under concurrency
- [ ] Partial payment → `partially_paid`; completing payment fires `InvoicePaid` with contract payload
- [ ] Overpayment rejected
- [ ] Payment posts balanced journal entry
- [ ] Void of paid invoice rejected; void of sent posts reversal
- [ ] `DealWon` creates draft stub (lines copied, never sent); inactive module = no-op
- [ ] Recurring command idempotent (run twice = one invoice per schedule)
- [ ] Overdue detection only touches sent invoices past due

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

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_invoices`, `fin_invoice_lines`, `fin_payments`, `fin_customers`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Fires | `InvoicePaid` → aging/dunning, cashflow, account rollup | [[../accounts-receivable/_module\|finance.ar]], [[../cash-flow/_module\|finance.cashflow]], crm |
| Consumes | `DealWon` → draft invoice stub via `CreateInvoiceStubListener` | [[../../crm/deals/_module\|crm.deals]] |
| Calls | `LedgerService::post` for payment journal entries | [[../general-ledger/_module\|finance.ledger]] |
| Reads | tax rates, currency rates (read-only) | [[../tax-management/_module\|finance.tax]], [[../multi-currency/_module\|finance.currency]] |

## Entity Notes

- [[architecture]] — service write-path, state machine, money, PDF/email/queues
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, rate limiter
- [[decisions]] — `fin_customers` new vs v1 spec
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/invoice-lifecycle]], [[features/recurring-invoices]], [[features/payments]]

## Related

- [[../general-ledger/_module]]
- [[../accounts-receivable/_module]]
- [[../tax-management/_module]]
- [[../../crm/deals/_module]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
