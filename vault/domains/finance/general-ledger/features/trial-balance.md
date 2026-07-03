---
domain: finance
module: general-ledger
feature: trial-balance
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Trial Balance

`TrialBalancePage` (#9 report custom page, [[../../../../architecture/ui-strategy]]): date-range selector with drill-down to journal lines.

- Backed by `LedgerService::trialBalance(from, to): TrialBalanceData`.
- Output rows: account_code, account_name, type, debit_cents, credit_cents — plus totals and period.
- Debits must equal credits over the range (covered by the brick/money fixture test).
- Closed periods are cached (`company:{id}:finance:trial-balance:{from}:{to}`, 1 h); posting into a period busts the key.
- Click an account → drill down to all `fin_journal_lines` for that account.

## UI
- **Kind**: custom-page (report)
- **Page**: `TrialBalancePage` — `/finance/ledger/trial-balance`
- **Layout**: date-range selector above a report table (account_code, account_name, type, debit_cents, credit_cents, totals); rows drill to journal lines.
- **Key interactions**: pick a from/to range; click an account row to drill down to its `fin_journal_lines`.
- **States**: empty (no postings in range) · loading (report compute / cache miss) · error (range invalid) · selected (drilled-into account).
- **Gating**: `finance.ledger.view-any` *(assumed)*.

## Data
- Owns / writes: nothing — read-only report over own `fin_accounts` + `fin_journal_lines` (money = integer minor units via brick/money; debits must equal credits across the range).
- Reads: own `fin_accounts`, `fin_journal_lines` only.
- Cross-domain writes: none — pure read model, no writes to any domain's tables ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no cross-domain events; reads GL tables the ledger owns.
- Feeds: nothing — output is display-only. Cached per closed period (`company:{id}:finance:trial-balance:{from}:{to}`, 1 h); an in-domain `LedgerService::post` busts the key.

## Test Checklist

### Unit
- [ ] Trial-balance rows sum `debit_cents` / `credit_cents` per account (brick/money, integer minor units); range totals: debits = credits
- [ ] Invalid range (from > to) rejected before compute

### Feature (Pest)
- [ ] `trialBalance(from, to)` over GL fixtures returns balanced totals (debits = credits) with correct per-account subtotals
- [ ] Closed-period result served from cache (`company:{id}:finance:trial-balance:{from}:{to}`); an in-period `LedgerService::post` busts the key and forces recompute
- [ ] Tenant isolation: company A trial balance excludes company B journal lines

### Livewire
- [ ] `TrialBalancePage` renders the report grid and drills an account row down to its `fin_journal_lines`; empty range shows the empty state
- [ ] `canAccess` denied without `finance.ledger.view-any` and when `finance.ledger` inactive

See [[../api]], [[../data-model]].
