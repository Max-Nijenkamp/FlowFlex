---
domain: ai
module: workflow-builder
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workflow Builder — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/event-bus]].

---

## Permissions

| Permission | Description |
|---|---|
| `ai.workflows.view-any` | View workflows + run history |
| `ai.workflows.manage` | Create / edit / enable / disable workflows |
| `ai.workflows.run-test` | Dry-run a workflow with a sample payload |

---

## Access Contract

```php
canAccess() = Auth::user()->can('ai.workflows.view-any')
           && BillingService::hasModule('ai.workflows')
```

Per [[../../../architecture/filament-patterns]] #1 — the builder page + resources state `canAccess()` explicitly; `manage` gates edit, `run-test` gates dry-run.

---

## Actions Execute Through Owning Services Only

- Every action runs through `WorkflowActionRegistry::execute` under `CompanyContext`, reaching the **owning module's service** — never a raw write into another domain's tables. This is the load-bearing security property: a workflow cannot escalate past what its actions' owning services allow ([[../../../security/data-ownership]]).
- **Module gating at save.** An action whose owning module is inactive for the company is rejected by `WorkflowGraphValidator` at save time — a tenant cannot wire an action for a capability it hasn't bought.
- **CompanyScope-bound.** Trigger resolution and every action bind to the acting company via the event's scalar `company_id`; a workflow never crosses tenants.

---

## Loop Guard

- Workflow-produced events carry a *(assumed)* system-actor flag so they do **not** re-trigger workflows (depth 1). Without this, "create task → task-created event → workflow → …" could cascade infinitely. See [[unknowns]] for the actor-tagging approach to confirm.

---

## Webhook Actions: Rate Limiting & SSRF

- **Rate limiter** (medium, per [[../../../build/security-audit-2026-06-11]]): outbound **webhook** actions and per-workflow execution are throttled, in addition to the loop guard — a hostile or misconfigured workflow must not become an SSRF-amplification or spam vector.
- Webhook targets should be validated (no internal/loopback addresses) *(assumed — confirm against [[../../../architecture/security]])*.

---

## Tenant Isolation

- `ai_workflows` + `ai_workflow_runs` scoped by `company_id` (`BelongsToCompany` + `CompanyScope`).
- `WorkflowTriggerListener` and `RunWorkflowJob` run under `WithCompanyContext` so matching, execution, and run logging all bind to the acting company.

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
