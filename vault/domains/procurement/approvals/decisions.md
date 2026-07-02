---
domain: procurement
module: approvals
type: decisions
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Approvals — Local Decisions

- **Role-based approvers for v1.** `approver_role` references a Spatie role, not a user. Simpler tenant setup; per-user overrides deferred. *(assumed)*
- **Approvals is a routing service, not a workflow store.** Consumers own their approval action rows. Keeps the bounded context clean and lets Approvals build before its consumers.
- **Default escalation SLA = 3 business days**, per-rule overridable. *(assumed)*
- **No matching rule → owner approves.** Fail-safe rather than fail-open. *(assumed)*

> [!warning] UNVERIFIED
> "Business days" vs calendar days for escalation is assumed; needs confirmation against a company-calendar/holiday source (none specified in vault).

## Related

- [[_module]] · [[unknowns]] · [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
