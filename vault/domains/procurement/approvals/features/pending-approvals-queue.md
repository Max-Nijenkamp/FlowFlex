---
domain: procurement
module: approvals
feature: pending-approvals-queue
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Unified Pending Approvals Queue

One inbox showing everything awaiting the current user's approval — requisitions and POs together — so an approver never hunts across modules.

## Behaviour

- Aggregates pending items where the current user (or their delegator) is the current-level approver.
- Each row: entity type, reference, amount, requester, waiting-since, escalation flag.
- Approve/reject acts are dispatched to the **owning** module's service (requisition or PO), which writes its own approval row.
- Mobile-friendly (approve from anywhere) — addresses the "approvals stall on desktop VPN" gap ([[../../_opportunities]]).

## UI

- **Kind**: custom-page
- **Page**: "Pending Approvals" (`/operations/procurement/approvals/pending`)
- **Layout**: single list, filter chips (type, escalated, amount band); row expands to line detail; bulk-approve action for same-type rows.
- **Key interactions**: approve/reject with comment (comment required on reject) → optimistic row removal + toast; escalated rows badged.
- **States**: empty ("You're all caught up") · loading (row skeletons) · error (toast + retry) · selected (row expanded to detail).
- **Gating**: `procurement.approvals.view-any`; the act itself re-checks the owning module's approve permission + current-level/delegate rule.

## Data

- Owns / writes: nothing of its own — a read/aggregation surface.
- Reads: `PendingApproval` read model populated by requisitions + POs.
- Cross-domain writes: none directly; approve/reject calls the owning module's service which writes its own tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: pending state + approve/reject services from [[../../requisitions/_module|requisitions]] and [[../../purchase-orders/_module|POs]].
- Feeds: nothing (delegates writes to owners).

## Test Checklist

### Unit
- [ ] `PendingApproval` read model merges requisition + PO items for the current user only

### Feature (Pest)
- [ ] Queue shows only items at the user's current approval level (incl. delegations); acting removes the item
- [ ] Tenant isolation: never lists another company's approvals

### Livewire
- [ ] `PendingApprovalsPage` canAccess() explicit; approve/reject inline actions delegate to the owning module's service

## Unknowns

- Bulk-approve across mixed entity types — restricted to single type v1? `*(assumed: yes)*`

## Related

- [[../_module|Approvals]] · [[escalation]] · [[../../requisitions/features/approval-flow]]
