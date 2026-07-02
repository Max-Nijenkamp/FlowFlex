---
domain: hr
module: payroll
feature: ledger-journal-posting
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Ledger Journal Posting

## Purpose
Hand off an approved payroll run to Finance for GL journal posting.

## Intended Behavior
- On approval, the run fires `PayrollRunApproved` carrying period totals and currency.
- finance.ledger consumes the event and posts the journal entry (soft dependency — if unbuilt, the event fires unconsumed).
- FlowFlex records and tracks payroll only; it does not move money — actual payment is via the company's bank or payroll provider.

## Tables / Permissions / Events
- Fires: `PayrollRunApproved` (payload in [[../api]])
- Soft dep: finance.ledger
- See [[../../../../architecture/event-bus]]

## UI

- **Kind**: background
- **Page**: none (fires on run approval — the trigger UI is the approve action on [[payroll-run-lifecycle]])
- **Layout**: no standalone screen — the approved run's detail page shows period totals and currency that constitute the event payload; the GL entry itself lives in the Finance panel
- **Key interactions**: none direct; approving a run (`hr.payroll.approve`) fires the event
- **States**: n/a (background) — event fires unconsumed when finance.ledger is unbuilt; FlowFlex records payroll only, never moves money
- **Gating**: no UI gate of its own; the upstream approve action requires `hr.payroll.approve`

## Data

- Owns / writes: none (read-only over `hr_payroll_runs` totals to build the event payload)
- Reads: `hr_payroll_runs` (period totals, currency) — own module
- Cross-domain writes: via events only — posts to Finance's GL by firing `PayrollRunApproved`, never writing finance tables ([[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: `PayrollRunApproved` → consumed by `finance.ledger` (posts the GL journal entry)
- Shared entity: none

Back to [[../_module]].
