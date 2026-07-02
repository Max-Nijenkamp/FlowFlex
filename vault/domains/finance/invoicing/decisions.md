---
domain: finance
module: invoicing
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Invoicing — Decisions

## `fin_customers` recipient record (new vs v1 spec)

A dedicated `fin_customers` table is intended to hold invoice recipients (name, email, billing address, VAT number, payment terms). It links to a CRM account via `crm_account_id` when [[../../crm/contacts/_module|crm.contacts]] is active, and stands alone otherwise. The original v1 spec had no separate customer record.

## Payments post through the ledger only

Recorded payments are intended to move invoice state and post a balanced journal entry via `LedgerService::post` (AR ↓ / cash ↑) — invoicing never writes ledger truth directly. Voids that touched the ledger flow through a reversal. This mirrors the [[../general-ledger/_module|general-ledger]] single-write-path rule.

## Money precision

Amounts are integer minor units via `brick/money`, never floats — see the strip-to-shell context in [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] and the currency-precision decision referenced from [[../_index]].

See [[unknowns]].
