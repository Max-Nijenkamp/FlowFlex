---
domain: procurement
module: requisitions
feature: create-requisition
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Create Requisition

An employee raises a purchase request: line items, justification, department, optional budget line. Starts in `draft`, submits into the approval flow.

## Behaviour

- Header: description, justification (required), department?, budget_line?, currency.
- Lines (≥1): free-text or catalogue item, quantity > 0, estimated unit cost. `estimated_cost_cents = Σ(qty × unit)`.
- Save as draft or submit; submit resolves the approval chain ([[approval-flow]]) and runs the budget check ([[budget-check]]).

## UI

- **Kind**: simple-resource
- **Page**: "Requisitions" (`/operations/procurement/requisitions`) with **My requisitions** / **Approval queue** tabs.
- **Layout**: table (number, requester, total, status badge, waiting-on) + create/edit form with a repeatable line-items table and catalogue picker ([[catalogue-picker]]).
- **Key interactions**: add/remove lines; live total; "Save draft" vs "Submit"; convert action on approved rows ([[convert-to-po]]).
- **States**: empty ("Raise your first requisition" CTA) · loading (table skeleton) · error (toast + field errors) · selected (row → infolist with approval timeline).
- **Gating**: view `procurement.requisitions.view-any` (own always visible); create `procurement.requisitions.create`.

## Data

- Owns / writes: `proc_requisitions`, `proc_requisition_items`.
- Reads: catalogue items (soft), budget lines (soft), departments (soft HR).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Feeds: on submit → [[approval-flow]]; on approve → `RequisitionApproved` event.
- Shared entity: catalogue items (procurement.catalogue), budget lines (finance), departments (HR).

## Unknowns

- Department free-text vs HR-linked when HR inactive. `*(assumed: nullable)*`

## Related

- [[../_module|Requisitions]] · [[approval-flow]] · [[catalogue-picker]] · [[budget-check]]
