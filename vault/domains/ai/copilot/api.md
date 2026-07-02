---
domain: ai
module: copilot
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Copilot — API / DTOs

The public surface is one input DTO plus the in-process `CopilotService` / `CopilotToolRegistry`. Copilot **provides no cross-domain API** — it is a consumer. It calls [[../model-config/api|ai.config]]'s `LlmGateway` and each domain's read services via registered tools.

---

## SendCopilotMessageData (input)

Written by `CopilotChat` (Livewire).

| Field | Type | Rules |
|---|---|---|
| `conversation_id` | ulid | nullable — existing (own) conversation, or null to start a new one; ownership checked |
| `content` | string | required, `max:8000` |
| `context` | array | nullable — `{ panel?, record_type?, record_id? }`; record access is permission-checked at tool-fetch time |

---

## CopilotService (command API — in-process)

`CopilotService::send(SendCopilotMessageData $data): …` — runs the agent loop:

- Resolves/creates the conversation (asserts the caller owns it).
- Calls `LlmGateway::complete('copilot', $messages, $opts)` — budget + feature toggle enforced there (`AiBudgetExceededException`, `AiFeatureDisabledException` surface as friendly errors).
- Offers the model only tools the current user is permitted to run.
- Executes tool calls via `CopilotToolRegistry::execute(key, args)`.
- Persists the assistant turn; streams tokens to the UI *(assumed: SSE)*.

---

## CopilotToolRegistry (registration + execution)

- `register(string $key, ToolDefinition $def)` — called by each domain at boot. `ToolDefinition = { schema, permission, module-key, handler }`.
- `execute(string $key, array $args)` — permission check → `hasModule` check → `CompanyScope`-bound handler. Returns **data only**; never raw SQL, never a writer.
- v1 tool set *(assumed)*: crm deal summary metrics, finance revenue/invoice metrics, hr headcount, support ticket lookup, record summarisation.

---

## Public / Portal Endpoints

None. Internal `/app` (or `/ai`) chat surface + in-process service API. No external HTTP routes. Streaming, if SSE, is served by an internal authenticated route on the chat page *(assumed)*.

> [!warning] UNVERIFIED
> Exact v1 tool set and the streaming transport are assumptions — see [[unknowns]].
