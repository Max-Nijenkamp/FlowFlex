---
domain: ai
module: copilot
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Copilot — Architecture

See also [[_module|ai.copilot._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/patterns/custom-pages]], [[../../../architecture/patterns/interface-service]], [[../../../architecture/security]] (prompt injection).

---

## Services & Actions

- **`CopilotService::send(SendCopilotMessageData): …`** — the agent loop. Resolves/creates the conversation, builds the message history, calls `LlmGateway::complete('copilot', …)`, offers the model only the tools the current user is permitted to run, executes any tool calls through `CopilotToolRegistry`, appends the assistant response, and streams tokens back to the UI *(assumed: streamed via SSE)*. Every LLM turn is metered by `LlmGateway`.
- **`CopilotToolRegistry::register(key, ToolDefinition)`** — domains register read-only tools at boot. A `ToolDefinition` = `{schema, permission, module-key, handler}`. `execute(key, args)` = permission check → `hasModule(module-key)` check → `CompanyScope`-bound handler. Tools that fail either gate are never surfaced to the model.
- Tools wrap **existing** domain services/metrics — they never issue free-form SQL and never write. This is the module's entire data path.

---

## Tool Registry & Guardrails

| Concern | Mechanism |
|---|---|
| Data access | Only via registered tools; no free-form queries or raw SQL exposed to the model. |
| Tenant isolation | Every handler runs under `CompanyContext` + `CompanyScope`; a tool cannot read another company. |
| Permission | Each tool declares a `permission`; execution is denied (and the tool hidden) when the user lacks it. The model is told "not permitted" rather than silently failing. |
| Module gating | A tool for a disabled module is never registered/offered. |
| Prompt injection | Tool results are wrapped as **data-only** content; the system prompt instructs the model to distrust instructions embedded in returned data. Outputs render as plain text — never executed, never raw HTML. |

---

## Filament Artifacts

**Nav group:** Copilot

| Artifact | Kind (ui-strategy row) | Notes |
|---|---|---|
| `CopilotPage` | #8-style chat custom page | Livewire chat with streaming; conversation sidebar. Hosts draft/summarise modes. |

Backing Livewire component: `App\Livewire\AI\CopilotChat`. Pattern reference: [[../../../architecture/patterns/custom-pages]], [[../../../architecture/ui-strategy]].

**Access contract:** `canAccess() = Auth::user()->can('ai.copilot.use') && BillingService::hasModule('ai.copilot')` per [[../../../architecture/filament-patterns]] #1 — the custom page states it explicitly.

---

## Rate Limiting

Per [[../../../build/security-audit-2026-06-11]] (medium): a per-user / per-company rate limiter (`RateLimiter` / throttle) guards copilot message sends **in addition to** the `LlmGateway` monthly budget. The budget bounds cost; the throttle bounds request rate/abuse.

---

## Jobs, Realtime & Search

- **Realtime:** streaming assistant tokens via SSE *(assumed)* — see [[unknowns]] for transport. No Reverb broadcast required for v1.
- No Meilisearch index for this module.
- No scheduled jobs (conversations pruned only via soft-delete / user action *(assumed)*).

> [!warning] UNVERIFIED
> The streaming transport (SSE vs. Livewire polling vs. Reverb) is assumed. Confirm against [[../../../architecture/websockets]] before build.
