---
domain: finance
module: bank-accounts
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Bank Accounts — Decisions

## IBAN / account number encrypted at rest

`iban` and `account_number` are intended to be stored encrypted (`encrypted` cast, `text` column), with masked last-4 display and the full value gated behind `finance.bank.view-sensitive`. This follows the Security required-encryption list. See [[../../../security/encryption]].

## Import never aborts on a bad row

The chunked import job is intended to skip and report bad rows rather than failing the whole statement, so a single malformed line cannot block a valid import. Dedupe is enforced by the `import_hash` unique constraint.

## Reconciliation requires exact amount match

`reconcile` throws `AmountMismatchException` unless the transaction and journal-line amounts match exactly — suggested matches use exact amount within a ±5-day window *(assumed)*.

## Money precision

Amounts are integer minor units via `brick/money`, never floats — see the strip-to-shell context in [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

See [[unknowns]].
