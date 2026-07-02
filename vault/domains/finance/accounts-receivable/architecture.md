---
domain: finance
module: accounts-receivable
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Receivable — Architecture

Interface→Service binding: `ArServiceInterface` → `ArService`, registered in the finance service provider per [[../../../architecture/patterns/interface-service]].

## Service surface

- `aging(?customerId)` returns bucketed open balances computed from `fin_invoices` + `fin_payments`.
- `statement(customerId, from, to)` assembles a per-customer ledger of invoices, payments, and running balance.
- `writeOff(WriteOffData)` voids the remaining balance and posts a GL bad-debt entry; the approver is recorded.
- `allocatePayment(AllocatePaymentData)` records per-invoice payments through `InvoiceService::recordPayment` — AR does not write invoice/payment rows directly, it delegates to the owning module.
- `dso(period)` computes days-sales-outstanding.

## GL posting path

Write-offs are the only AR action that touches the ledger. `writeOff` posts a balanced bad-debt entry through the invoicing/ledger services rather than inserting journal lines itself, keeping `LedgerService` the single sanctioned write path (see [[../general-ledger/_module]]).

## Dunning job

`ProcessDunningCommand` runs daily on the notifications queue. For each overdue invoice it evaluates the active `fin_ar_dunning_rules` in `escalation_level` order and fires the next level once, guarded by `fin_invoices.last_dunning_level` *(assumed column)*. Reminder mail goes out via `DunningMail` (queued mailable). Payment resets the level — see the event flow below.

## Money handling

All amounts are integer **minor units** (cents) in `bigint` columns, manipulated with `brick/money` — never raw floats. Aging totals, DSO, and write-off amounts all flow through `Money`. See [[../../../architecture/packages]] (brick/money) and [[../../../architecture/performance]].

## Event flow

Consumes `InvoicePaid` (from [[../invoicing/_module|finance.invoicing]]): `UpdateARAgingListener` recomputes the account's aging cache and resets its dunning level. Queued, `WithCompanyContext`, per the [[../../../architecture/event-bus]] contract.

See [[data-model]], [[api]], [[../../../architecture/queue-jobs]].
