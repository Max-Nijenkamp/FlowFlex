---
domain: finance
module: accounts-payable
feature: bill-approval
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Test Checklist

### Unit
- [ ] Threshold routing selects `finance.ap.approve` below the configured amount and `finance.ap.approve-large` at/above it *(assumed: single threshold)*
- [ ] Bill-line amounts sum to the bill amount (brick/money integer cents cross-check)

### Feature (Pest)
- [ ] `approveBill` on a valid draft posts a balanced GL liability entry via `LedgerService::post` and transitions `draft → approved` under a pessimistic lock
- [ ] Duplicate `(supplier, bill_number)` is rejected on create
- [ ] 3-way match blocks approval on PO/GRN mismatch when procurement is active (`MatchFailedException`); bypassed when inactive; tenant isolation on the bill query

### Livewire
- [ ] `BillResource` approve action is visible/enabled only with the routed permission; `canAccess` denied without `finance.ap.view-any`
- [ ] Status badge column reflects the `BillState` and offers only valid transition actions

## Related

- [[../_module|Accounts Payable]] · [[three-way-match]] · [[payment-runs]] · [[../../general-ledger/_module]]
