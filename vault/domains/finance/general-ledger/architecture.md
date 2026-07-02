---
domain: finance
module: general-ledger
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# General Ledger — Architecture

Interface→Service binding: `LedgerServiceInterface` → `LedgerService`, registered in `FinanceServiceProvider`. This service is **the only write path to the ledger**.

## Write path & immutability

- All posting goes through `LedgerService::post`, wrapped in `DB::transaction`. It validates that debits equal credits and that the entry date falls in an open fiscal period before committing.
- Posted entries are never updated or deleted. Corrections are made via `reverse`, which writes a mirrored (swapped debit/credit) entry and leaves the original untouched.
- Invoice/payment/expense postings are direct service calls **within** the finance domain (same-domain rule, no events). Only cross-domain triggers (payroll) arrive as events.

## Money handling

All amounts are integer **minor units** (cents) stored in `bigint` columns and manipulated with `brick/money` — never raw float math. Each journal line carries exactly one non-zero of `debit_cents` / `credit_cents`. See [[../../../architecture/packages]] (brick/money) and [[../../../architecture/performance]].

## Period locking

`fin_fiscal_periods` gates postings: a `closed` period rejects new entries (`ClosedPeriodException`). Closing/reopening is an owner-level, audited operation.

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/event-bus]], [[data-model]], [[api]].
