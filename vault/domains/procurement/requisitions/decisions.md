---
domain: procurement
module: requisitions
type: decisions
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Requisitions — Local Decisions

- **Chain snapshotted on submit.** The approval chain is frozen into `proc_requisition_approvals` at submit time; later matrix edits don't re-route in-flight requisitions.
- **Budget check warns, does not block.** Soft dep on finance.budgets; over-budget attaches a warning but allows submission. *(assumed)* — competitors like ProcureDesk *block*; see [[../_opportunities]] for the hard-block differentiator.
- **Conversion delegates to Operations.** This module sets `po_id` only; the PO row is created by `operations.purchase-orders`.
- **Reject requires a comment; resubmit starts a fresh chain** (not a resume).
- **Templates = duplicate action v1** rather than a first-class template entity. *(assumed)*

## Related

- [[_module]] · [[unknowns]] · [[../_opportunities]]
