---
domain: procurement
module: approvals
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
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

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../architecture/patterns/actions-pattern]] · [[../../../architecture/queue-jobs]]
