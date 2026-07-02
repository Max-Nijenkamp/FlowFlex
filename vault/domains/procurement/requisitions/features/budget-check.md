---
domain: procurement
module: requisitions
feature: budget-check
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Budget Check

On submit, validate the requisition total against the department/budget-line remaining and surface a warning when over.

## Behaviour

- Soft dep: only active when `finance.budgets` is on; otherwise a no-op.
- Reads `BudgetService::remaining(budget_line_id)` (read-only) and compares to the requisition total.
- Over budget → **warning** attached to the submission (does not block v1). A per-company hard-block toggle is a candidate differentiator ([[../../_opportunities]]).

## UI

- **Kind**: widget (inline banner within the requisition form/infolist; no standalone page).
- **Page**: none — renders as a callout on `RequisitionResource` create/edit + a badge on over-budget rows.
- **Layout**: coloured callout — "€X over the remaining €Y for {budget line}".
- **Key interactions**: none beyond acknowledging; submit still allowed.
- **States**: hidden (budgets inactive / within budget) · warning (amber callout) · loading (skeleton while remaining fetched) · error (silently degrade — never blocks submit).
- **Gating**: visible with `procurement.requisitions.create`; budget figures require read access exposed by finance.

## Data

- Owns / writes: nothing.
- Reads: `finance.budgets` `BudgetService::remaining()` (read-only).
- Cross-domain writes: none. Budget *commitment* (if any) happens via Finance's own listener on `RequisitionApproved` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `finance.budgets` read API.
- Feeds: nothing directly.

## Unknowns

- Warn vs hard-block (per-company toggle). **UNVERIFIED** — differentiator candidate.
- Does approval commit budget, or only conversion/PO send? `*(assumed: on PO send, via finance listener)*`

## Related

- [[../_module|Requisitions]] · [[../../finance/budgets/_module]] · [[../../_opportunities]]
