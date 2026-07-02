---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.approvals
status: planned
build-status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.notifications]
soft-depends: [procurement.requisitions, procurement.purchase-orders]
fires-events: []
consumes-events: []
patterns: []
tables: [proc_approval_rules, proc_approval_delegations]
permission-prefix: procurement.approvals
encrypted-fields: []
last-reviewed: 2026-07-02
color: "#4ADE80"
---

# Procurement Approvals

Configurable approval workflows and authority matrix for requisitions and POs based on amount thresholds, category, and department. Builds first in Procurement — requisitions and POs route through it.

Hosted in the **/operations** panel (Procurement nav → Settings). No panel of its own — see [[../_index|Procurement MOC]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, escalation notifications |
| Soft | [[../requisitions/_module\|requisitions]] / [[../purchase-orders/_module\|procurement POs]] | the consumers of the matrix |

---

## Core Features

- [[features/approval-matrix\|Approval matrix & routing]] — who approves what by amount/category/level; `ApprovalMatrix::chainFor(...)`.
- [[features/delegation\|Approver delegation]] — date-ranged delegation while away.
- [[features/escalation\|SLA escalation]] — auto-escalate stale approvals after `escalation_days`.
- [[features/pending-approvals-queue\|Unified pending queue]] — cross-entity approval inbox per user.

---

## Data Model

Full model + ERD: [[data-model]]. Owns `proc_approval_rules`, `proc_approval_delegations`.

## DTOs

`CreateApprovalRuleData`, `CreateDelegationData` — see [[api]].

## Services & Actions

`ApprovalMatrix` (support class), `EscalateStaleApprovalsCommand`. See [[api]] + [[architecture]].

---

## Filament

**Nav group:** Settings (within /operations)

| Artifact | UI kind | Feature |
|---|---|---|
| `ApprovalRuleResource` | simple-resource | [[features/approval-matrix]] |
| `ApprovalDelegationResource` | simple-resource | [[features/delegation]] |
| `PendingApprovalsPage` | custom-page | [[features/pending-approvals-queue]] |

**Access contract:** every artifact gates on `canAccess() = Auth::user()->can('procurement.approvals.view-any') && BillingService::hasModule('procurement.approvals')` per [[../../../architecture/filament-patterns]] #1. See [[security]].

---

## Permissions

`procurement.approvals.view-any` · `procurement.approvals.manage-rules` · `procurement.approvals.delegate-own`

Module-scoped: assignable only while `procurement.approvals` is active — [[../../core/rbac/_module]].

---

## Cross-Domain Edges

- **Consumes (read):** `core.rbac` role names (approver_role); `hr.org` departments (soft, category/dept routing); `core.notifications` for escalation delivery.
- **Fires:** no cross-domain events — it is a routing/read service other procurement modules call synchronously (`ApprovalMatrix::chainFor`).
- **Data ownership:** writes **only** `proc_approval_rules`, `proc_approval_delegations`. Approval *actions* are recorded by each consumer in its own tables (e.g. `proc_requisition_approvals`). See [[../../../security/data-ownership]].

Detail: [[decisions]] · [[unknowns]].

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] `chainFor` picks correct levels per amount/category fixtures
- [ ] Overlapping amount ranges rejected at save
- [ ] Delegation: delegate can act within date range; outside range rejected
- [ ] Escalation fires once per level after SLA
- [ ] No matching rule → sensible default (owner approves *(assumed)*)

## Build Manifest

```
database/migrations/xxxx_create_proc_approval_rules_table.php
database/migrations/xxxx_create_proc_approval_delegations_table.php
app/Models/Procurement/{ApprovalRule,ApprovalDelegation}.php
app/Data/Procurement/{CreateApprovalRuleData,CreateDelegationData}.php
app/Support/Procurement/ApprovalMatrix.php
app/Console/Commands/Procurement/EscalateStaleApprovalsCommand.php
app/Filament/Operations/Resources/{ApprovalRuleResource,ApprovalDelegationResource}.php
app/Filament/Operations/Pages/PendingApprovalsPage.php
database/factories/Procurement/ApprovalRuleFactory.php
tests/Feature/Procurement/{ApprovalMatrixTest,DelegationTest}.php
```

## Related

- [[../requisitions/_module]] · [[../purchase-orders/_module]] · [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
