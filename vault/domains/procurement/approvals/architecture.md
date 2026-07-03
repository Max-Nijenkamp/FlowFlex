---
domain: procurement
module: approvals
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Approvals — Architecture

## Shape

A **read/routing service**, not a workflow engine that owns approval records. Consumers (requisitions, POs) ask the matrix *who must approve this amount/category*, then record the actual approval actions in their **own** tables. This keeps Approvals a thin, dependency-light module that builds first.

```mermaid
flowchart LR
    R[requisitions / procurement POs] -->|chainFor type,amount,category| M[ApprovalMatrix]
    M -->|reads| RULES[(proc_approval_rules)]
    M -->|resolves| DEL[(proc_approval_delegations)]
    M -->|role→user| RBAC`core.rbac read`
    R -->|records action| RT[(consumer's own approval table)]
    SCHED[EscalateStaleApprovalsCommand] -->|scan stale| RT
    SCHED --> N`core.notifications`
```

## Key decisions

- **Support class, not Interface→Service.** `ApprovalMatrix` is a stateless resolver (`app/Support/Procurement/`), no persisted state of its own beyond rules/delegations. Simple ops → [[../../../architecture/patterns/actions-pattern]].
- **Role-based approvers v1** — `approver_role` is a Spatie role name; per-user rules deferred. See [[unknowns]].
- **Delegations resolved at act time**, not at chain-build time, so a delegation added after submission still applies.
- **Escalation** is a scheduled command scanning consumer approval tables via a shared read contract (`PendingApproval` read model) — it never writes consumer tables, only fires notifications.

## Filament Artifacts

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ApprovalRuleResource` | #1 CRUD resource | badge-status, guarded-delete | Matrix rules: applies_to x category x amount range -> approver role |
| `ApprovalDelegationResource` | #1 CRUD resource | date-range filter | Delegations with validity windows |
| `PendingApprovalsPage` | #8 inbox custom page ([[../../../architecture/patterns/page-blueprints#Inbox]]) | cross-module queue | Requisitions + POs awaiting the current user, via the `PendingApproval` read model |

Hosted in the **/operations** panel (Procurement nav). Every artifact gates on `canAccess() = Auth::user()->can('procurement.approvals.view-any') && BillingService::hasModule('procurement.approvals')` per [[../../../architecture/filament-patterns]] #1 -- `PendingApprovalsPage` states it explicitly.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Rule / delegation CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |
| `chainFor` resolution | n-a | Stateless read; consumers record actions in their own tables under their own locks |
| `EscalateStaleApprovalsCommand` | n-a | Single scheduled writer; notification-only, never writes consumer tables |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../architecture/patterns/actions-pattern]] · [[../../../architecture/queue-jobs]]
