---
domain: finance
module: accounts-receivable
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Receivable — Decisions

## Dunning tracking on the invoice (new vs v1 spec)

Dunning state is intended to be tracked on the invoice itself via a `fin_invoices.last_dunning_level` int column added by this module. This guards `ProcessDunningCommand` so each escalation level fires only once and lets `InvoicePaid` reset the sequence. *(assumed)*

## Credit-limit tracking (new vs v1 spec)

A `fin_customers.credit_limit_cents` column is intended to be added by this module for per-customer credit-limit tracking. *(assumed)*

## Write-offs post through ledger services

Write-offs do not insert journal lines directly — they post a bad-debt entry through the invoicing/ledger services, keeping `LedgerService` the single sanctioned write path (see [[../general-ledger/decisions]]).

## Money precision

Amounts are integer minor units via `brick/money`, never floats — see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] context.

See [[unknowns]].
