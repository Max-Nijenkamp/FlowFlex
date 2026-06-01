---
type: module
domain: Procurement
panel: operations
module-key: procurement.approvals
status: planned
color: "#4ADE80"
---

# Procurement Approvals

Configurable approval workflows and authority matrix for requisitions and POs based on amount thresholds, category, and department.

## Core Features

- Approval matrix: who approves what, by amount threshold and category
- Multi-level chains: e.g. <€1k manager, €1k–10k director, >€10k CFO
- Delegation: approver can delegate while away
- Approval routing: auto-route to correct approver based on rules
- Approval audit trail
- Escalation: auto-escalate if not actioned within SLA
- Approval dashboard: pending approvals per user

## Data Model

| Table | Key Columns |
|---|---|
| `proc_approval_rules` | company_id, applies_to (requisition/po), min_amount_cents, max_amount_cents, category, approver_role, level |
| `proc_approval_delegations` | company_id, delegator_id, delegate_id, start_date, end_date |

## Filament

**Nav group:** Settings

- `ApprovalRuleResource` — configure the authority matrix
- `ApprovalDelegationResource` — manage delegations
- Pending approvals queue (shared across requisitions + POs)

## Cross-Domain

- Drives approval routing in requisitions and procurement POs

## Related

- [[domains/procurement/requisitions]]
- [[domains/procurement/purchase-orders]]
