---
domain: finance
module: accounts-payable
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Payable — Decisions

## Suppliers table owned by AP (new vs v1 spec)

`fin_suppliers` is introduced by AP, carrying the encrypted IBAN, VAT number, and payment-terms default. Marked *(new vs v1 spec)* in the source.

## SEPA export deferred (new vs v1 spec)

Payment runs are intended to produce a batch list export for v1. A full SEPA `pain.001` CSV/XML export is deferred. *(assumed)*

## Void state on bills

A `voided` transition from `draft`/`approved` is intended, reversing the GL posting if one exists. *(assumed)*

## Single approval threshold

Approval routing uses a single amount threshold from company settings; amounts above it require `finance.ap.approve-large`. *(assumed)*

## Postings go through ledger services

Liability (on approval) and cash (on payment) entries post through the ledger services, never via direct journal inserts — `LedgerService` stays the single sanctioned write path (see [[../general-ledger/decisions]]).

## Money precision

Amounts are integer minor units via `brick/money`, never floats — see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] context.

See [[unknowns]].
