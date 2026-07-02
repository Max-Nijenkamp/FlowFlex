---
domain: procurement
module: requisitions
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Requisitions — Architecture

## Shape

Interface→Service (`RequisitionServiceInterface` → `RequisitionService`) because the flow is multi-step and stateful (state machine + chain resolution + conversion). Bound in `ProcurementServiceProvider`. Pattern: [[../../../architecture/patterns/interface-service]].

```mermaid
flowchart TD
    U[Requester] -->|CreateRequisitionData| S[RequisitionService.submit]
    S -->|chainFor requisition,amount,cat| M`approvals.ApprovalMatrix`
    S -->|remaining| B`finance.budgets read`
    S -->|writes| RT[(proc_requisitions + items + approvals)]
    A[Approver] -->|ApproveRequisitionData| S2[RequisitionService.act]
    S2 -->|writes action| RT
    S2 -->|on final approve| EV{{RequisitionApproved}}
    S3[RequisitionService.convertToPo] -->|createFromRequisition| PO`operations.PurchaseOrderService`
```

## State machine (spatie/laravel-model-states)

`draft → submitted → approved | rejected → converted_to_po`. States in `app/States/Procurement/Requisition/`. Transitions guarded (approver ≠ requester; current-level only). Pattern: [[../../../architecture/patterns/states]].

## Key decisions

- **Chain resolved on submit**, snapshotted into `proc_requisition_approvals` so later matrix edits don't rewrite in-flight requisitions.
- **Budget check is a warning, not a block** — soft dep, degrades to no-op when finance.budgets inactive. See [[decisions]].
- **Conversion delegates PO creation to Operations** — this module only sets `po_id` on its own row after Operations returns the PO id.
- **Money** as integer cents via brick/money; `estimated_cost_cents = Σ items`.

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../architecture/patterns/interface-service]] · [[../../../architecture/patterns/states]]
