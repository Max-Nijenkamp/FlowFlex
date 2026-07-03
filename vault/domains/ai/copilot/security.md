---
domain: ai
module: copilot
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Copilot — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/security]] (prompt injection).

---

## Permissions

| Permission | Description |
|---|---|
| `ai.copilot.use` | Open the copilot chat and send messages |
| *(per-tool)* | Every registered tool checks its own domain permission at call time (e.g. `crm.deals.view-any`, `hr.employees.view-any`) |

---

## Access Contract

```php
canAccess() = Auth::user()->can('ai.copilot.use')
           && BillingService::hasModule('ai.copilot')
```

Per [[../../../architecture/filament-patterns]] #1 — the `CopilotPage` custom page states `canAccess()` explicitly.

---

## The Tool Boundary Is the Security Boundary

- **Tools are the only data path.** The model never sees raw SQL and cannot free-form query. Every data read goes through a registered `ToolDefinition` whose handler wraps an existing, already-authorised domain service.
- **Per-tool permission check.** Execution runs the asking user's permission for that tool's domain *before* the handler; a denied tool is not offered to the model, and denial is reported as "not permitted" (no silent leak).
- **Module gating.** Tools for disabled modules are never registered — the model can't invoke a capability the tenant hasn't bought.
- **CompanyScope-bound.** Handlers run under `CompanyContext` + `CompanyScope`; a tool cannot cross tenants ([[../../../security/tenancy-isolation]]).
- **Copilot writes nothing outside its own tables.** No cross-domain writes; other-domain access is strictly read-only ([[../../../security/data-ownership]]).

---

## Prompt Injection Defence

- Tool results are wrapped as **data-only** content; the system prompt instructs the model to distrust instructions embedded in returned data.
- Assistant output is rendered as **plain text** — never executed, never raw HTML.
- Test fixture: an instruction embedded in a tool result must not be followed (best-effort system-prompt assertion *(assumed)*).

---

## Conversation Privacy & Rate Limiting

- `ai_copilot_conversations` are **private to their owning user** — a second-layer `user_id` filter on top of `CompanyScope`; other users in the same company cannot read them.
- **Rate limiter** (medium, per [[../../../_archive/build-history/security-audit-2026-06-11]]): a per-user / per-company `RateLimiter` throttle on message sends, in addition to the `LlmGateway` monthly budget hard-stop.

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
