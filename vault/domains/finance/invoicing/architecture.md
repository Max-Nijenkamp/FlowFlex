---
domain: finance
module: invoicing
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Invoicing — Architecture

Interface→Service binding: `InvoiceServiceInterface` → `InvoiceService`, registered in the Finance service provider. The service owns invoice creation, sending, payment recording, and voiding.

## State machine

`fin_invoices.status` is a `spatie/laravel-model-states` machine (`InvoiceState`): `draft → sent → partially_paid → paid`, with `overdue` and `voided` branches. Transitions carry side effects (number assignment, PDF, mail, ledger postings, `InvoicePaid`). Full transition table in [[features/invoice-lifecycle]]. See [[../../../architecture/patterns/states]].

## Write path & ledger

Payments never write the ledger directly. `recordPayment` moves the invoice state and calls `LedgerService::post` (AR ↓ / cash ↑) — a direct in-domain service call, no event. Voids that touched the ledger post a reversal entry via the same service. See [[../general-ledger/_module]] for the sanctioned write path.

## Money handling

All amounts are integer **minor units** (cents) in `bigint` columns, manipulated with `brick/money` — never raw float math. Line totals round per line and sum *(assumed)*; discount and tax totals derive from the line set. `currency` is base unless [[../multi-currency/_module|finance.currency]] is active, in which case `exchange_rate` applies. See [[../../../architecture/packages]] (brick/money).

## PDF, email & queues

- `GenerateInvoicePdfJob` (exports queue) renders the invoice via `spatie/laravel-pdf` (Chromium, CSS-rendered) on send, overwriting `pdf_path` (tenant-scoped).
- `InvoiceMail` / `PaymentReminderMail` are queued on the notifications queue.
- `GenerateRecurringInvoicesCommand`, `MarkOverdueInvoicesCommand`, and `SendPaymentReminderCommand` run on schedule (finance / notifications queues). See [[../../../architecture/queue-jobs]] and [[../../../architecture/email]].

## Numbering

Invoice numbers are assigned at first send, never reused, gap-free per company *(assumed: advisory lock)*. Assignment is audited.

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/event-bus]], [[data-model]], [[api]].
