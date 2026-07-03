---
domain: finance
module: invoicing
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** Billing

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `InvoiceResource` | #1 CRUD resource | tweaks: state-badge-column (lifecycle badge + transition actions), custom-header-actions (send / record-payment / void), inline-relation-repeater (invoice line items), pdf-preview-panel (rendered invoice PDF) | edit form enabled only while `draft`; payments relation manager lists prior payments; list filters: status, customer, due date; Excel export |
| `CustomerResource` | #1 CRUD resource | — | standalone `fin_customers`; linked to CRM account when `crm.contacts` active |
| `InvoiceStatsWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | outstanding / overdue / paid totals; polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.invoicing.view-any') && BillingService::hasModule('finance.invoicing')`
per [[../../../architecture/filament-patterns]] #1. The record-payment slide-over and the send/void header actions run inside `InvoiceResource` (no standalone custom page), each additionally gated on its own permission. No public/portal surface in this module — the invoice PDF is delivered by email, not a public link *(assumed)*.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Invoice + line CRUD (draft, form/API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Send (draft → sent; gap-free number assignment) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the numbering counter — state transition per [[../../../architecture/patterns/states]]; number never reused |
| Record payment (money; posts GL entry, moves state) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the invoice — re-read open balance, reject overpayment, `LedgerService::post`, transition |
| Void (state transition; ledger reversal) | Pessimistic | `DB::transaction()` + `lockForUpdate()`; reversal entry if anything posted, `CannotVoidPaidInvoiceException` on paid |
| Recurring generation / overdue marking (scheduled commands) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the source row; `next_recurring_at` advanced in the same transaction (idempotent WHERE) |
| Invoice/PDF reads, list, stats widget | n-a | read-only / derived — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/event-bus]], [[data-model]], [[api]].
