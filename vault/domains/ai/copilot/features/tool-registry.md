---
domain: ai
module: copilot
feature: tool-registry
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Tool Registry

The permission-guarded, CompanyScope-bound execution path that is the **only** way copilot touches company data. Domains register read-only tools; the registry decides which are offered to the model and enforces authorisation at execution. This is the copilot security boundary.

## Behaviour

- `CopilotToolRegistry::register(key, ToolDefinition)` — `ToolDefinition = {schema, permission, module-key, handler}`; each domain registers its tools in its own provider.
- **Offered set** = tools whose `module-key` is active for the company **and** whose `permission` the asking user holds. Tools failing either test are never presented to the model.
- **Execution** = re-check the user's permission → run the handler under `CompanyContext` + `CompanyScope` → return the result as **data-only** content (wrapped so embedded instructions are distrusted).
- Handlers wrap existing, already-authorised domain services/metric queries — never raw SQL, never a cross-domain write.
- A denied tool returns "not permitted" to the model (no silent leak).

## UI

- **Kind**: background   <!-- a registry + execution guard; no screen of its own -->
- Surfaces indirectly inside [[chat-console|Chat Console]] as the inline "used <tool>" cards on assistant turns.

## Data

- Owns / writes: nothing. It writes no tables.
- Reads: other domains' metrics/records **read-only** through their services (crm, finance, hr, support — v1 set *(assumed)*).
- Cross-domain writes: none — the registry is the mechanism that *guarantees* copilot only ever reads other domains ([[../../../../security/data-ownership]]).

## Relations

- Reads: crm/finance/hr/support services (permission-checked, tenant-scoped).
- Feeds: the tool set + results back into [[chat-console|Chat Console]]'s agent loop.
- Shared entity: each tool's declared permission belongs to its owning domain's permission set ([[../../../../domains/core/rbac/_module|core.rbac]]).

## Test Checklist

### Unit
- [ ] `ToolDefinition` shape `{schema, permission, module-key, handler}` validated on registration.
- [ ] Offered-set filter: a tool is offered only when its `module-key` is active AND the user holds its `permission`.

### Feature (Pest)
- [ ] Execution re-checks the user's permission before running the handler; a denied tool returns "not permitted" (no silent leak).
- [ ] Handler runs under `CompanyContext` + `CompanyScope`: a tool cannot read another company's data (per-tool tenant test).
- [ ] A tool for an inactive module is never registered/offered to the model.
- [ ] Tool results are returned as data-only content (embedded instructions not surfaced as commands — prompt-injection fixture, best-effort *(assumed)*).

## Unknowns

> [!warning] UNVERIFIED
> The exact v1 tool set and each tool's declared permission are assumed (crm deal metrics, finance revenue/invoice metrics, hr headcount, support ticket lookup, record summarisation). Confirm before build. See [[../unknowns]].

## Related

- [[../_module|AI Copilot]] · [[chat-console|Chat Console]] · [[draft-and-summarise|Draft & Summarise]]
- [[../security]] · [[../../../../security/data-ownership]] · [[../../../../architecture/security]] (prompt injection)
