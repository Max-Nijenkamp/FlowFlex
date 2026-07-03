---
domain: finance
module: general-ledger
feature: fiscal-period-lock
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Fiscal Period Lock

Prevents retroactive edits by locking closed periods (`fin_fiscal_periods`).

- `FiscalPeriodResource` (#1 CRUD with status toggle) exposes close/reopen.
- `LedgerService::closePeriod(period)` / `reopenPeriod(period)` — owner-level (`finance.ledger.close-period`), audited.
- Any `post` whose `entry_date` falls in a `closed` period throws `ClosedPeriodException`.
- The `PostPayrollJournalEntryListener` retries when it hits a closed period (per event-bus contract).

## UI
- **Kind**: simple-resource
- **Page**: `FiscalPeriodResource` — `/finance/ledger/periods`
- **Layout**: table of periods (name, range, status) with a close/reopen status toggle action per row.
- **Key interactions**: close a period (locks it); reopen a closed period (owner-level, audited).
- **States**: empty (no periods defined) · loading (list) · error (`ClosedPeriodException` surfaced when a post targets a closed period) · selected (period row with toggle).
- **Gating**: `finance.ledger.close-period` (owner-level).

## Data
- Owns / writes: `fin_fiscal_periods`, and the ledger's own `fin_journal_*` — this feature is part of the GL, the owning module, so writing `fin_journal_*` here is its own right (money = integer minor units via brick/money). GL posts still go through `LedgerService::post`, never raw inserts.
- Reads: own `fin_journal_*` entry dates to enforce the close boundary.
- Cross-domain writes: none — other domains post into the GL via `LedgerService::post`, never by writing `fin_journal_*` directly ([[../../../../security/data-ownership]]).

## Relations
- Consumes: `PayrollRunApproved` (hr.payroll) indirectly — `PostPayrollJournalEntryListener` posts into the GL and retries when it hits a closed period.
- Feeds: `ClosedPeriodException` raised by `LedgerService::post` for any entry dated inside a closed period; consumed in-domain by posting flows and by the payroll listener's retry logic.

## Test Checklist

### Unit
- [ ] Period-membership check: an `entry_date` inside a `closed` period is flagged; the same date inside an `open` period passes

### Feature (Pest)
- [ ] `LedgerService::post` with an `entry_date` in a closed period throws `ClosedPeriodException`; posting into an open period succeeds
- [ ] `closePeriod` / `reopenPeriod` are gated by `finance.ledger.close-period`, write under `DB::transaction()` + `lockForUpdate()`, and are audited
- [ ] Tenant isolation: company A cannot close/reopen or post into company B's fiscal periods

### Livewire
- [ ] `FiscalPeriodResource` close/reopen toggle action runs the transition and surfaces `ClosedPeriodException` messaging on a blocked post
- [ ] `canAccess` denied without `finance.ledger.close-period` and when `finance.ledger` inactive

See [[../api]], [[../security]].
