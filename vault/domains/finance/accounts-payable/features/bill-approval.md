---
domain: finance
module: accounts-payable
feature: bill-approval
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Bill Approval Workflow

Route supplier bills through approval by **amount threshold** before they can be scheduled or paid.

- Bill status machine: `draft → approved → scheduled → paid` (spatie/laravel-model-states).
- Threshold routing: bills above a company-configured amount require the `finance.ap.approve-large`
  permission; below it, `finance.ap.approve`. *(assumed: single threshold — see [[../unknowns]])*.
- Approval posts a balanced **liability** entry to the General Ledger.
- Guarded by [[../security]] (permission + module gating).

## UI

- **Kind**: simple-resource
- **Page**: `BillResource` under `/finance/ap/bills` (list + view/edit, approve action, status badge)
- **Layout**: table of bills with status badge column; view page shows bill lines + supplier; approve action button
- **Key interactions**: approve/reject via action; status machine `draft → approved → scheduled → paid` drives available actions
- **States**: empty (no bills) · loading (table skeleton) · error (approve/post failure surfaced on action) · selected (a bill row/view)
- **Gating**: below threshold `finance.ap.approve`; above threshold `finance.ap.approve-large` *(assumed: single threshold — see [[../unknowns]])*

## Data

- Owns / writes: `fin_bills`, `fin_bill_lines`, `fin_suppliers`. Money as integer minor units (cents) via brick/money.
- Reads: own tables
- Cross-domain writes: approval posts a balanced **liability** GL entry via `LedgerService::post` — never writes `fin_journal_*` directly ([[../../../../security/data-ownership]])

## Relations

- Consumes: no events (bills may be drafted by [[three-way-match]] from `GoodsReceived`)
- Feeds: approved/scheduled bills consumed by [[payment-runs]]; posts to [[../../general-ledger/_module]] via `LedgerService::post`
- in-domain: `LedgerService::post` (finance.ledger) called on approval

## Related

- [[../_module|Accounts Payable]] · [[three-way-match]] · [[payment-runs]] · [[../../general-ledger/_module]]
