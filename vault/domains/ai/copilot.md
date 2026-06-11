---
type: module
domain: AI & Automation
domain-key: ai
panel: ai
module-key: ai.copilot
status: planned
priority: p3
depends-on: [ai.config, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [ai_copilot_conversations, ai_copilot_messages]
permission-prefix: ai.copilot
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# AI Copilot

Cross-domain AI assistant. Natural-language queries over company data, draft generation (emails, descriptions), and summarisation. All LLM calls through [[domains/ai/model-config|ai.config]]'s `LlmGateway`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/ai/model-config\|ai.config]] | LlmGateway: provider, budget, usage |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

(Data tools per domain available only when that module is active AND the user holds the matching permission.)

---

## Core Features

- Chat interface: ask questions about company data ("how many deals closed this month?")
- **Tool registry**: domains register read-only copilot tools — each tool wraps an existing service/metric query, runs under CompanyScope AND checks the asking user's permission before execution; tool results are data, never raw SQL access
- Draft generation: email replies, product descriptions, job postings
- Summarisation: documents, ticket threads, meeting notes (content passed in, permission-checked at fetch)
- Context awareness: panel/record context passed as structured metadata
- **Guardrails** ([[architecture/security]]): tools are the only data path (no free-form queries); tool inputs validated; prompt-injection: tool results wrapped as data-only content, system prompt instructs distrust of embedded instructions; outputs rendered as text (never executed/HTML)
- Usage metered per message via LlmGateway

---

## Data Model

### ai_copilot_conversations — id, company_id (indexed), user_id FK, title, created_at, deleted_at
### ai_copilot_messages — id, conversation_id FK, company_id, role (user/assistant/tool), content (text), tool_calls (jsonb nullable), tokens_used, created_at

Conversations are **private to their user** (second-layer scope). Provider config lives in ai.config (v1 spec's `ai_copilot_config` table dropped *(assumed)*).

---

## DTOs

### SendCopilotMessageData — conversation_id (own) or new, content (required, max:8000), context {panel?, record_type?, record_id?} (record access permission-checked)

## Services & Actions

- `CopilotService::send(...)` — agent loop: LlmGateway + tool registry; streams response *(assumed: streamed via SSE)*
- `CopilotToolRegistry::register(key, ToolDefinition)` — definition = {schema, permission, module-key, handler}; execution = permission check → CompanyScope-bound handler
- v1 tool set *(assumed)*: crm.deals summary metrics, finance revenue/invoice metrics, hr headcount, support ticket lookup, record summarisation

---

## Filament

**Nav group:** Copilot

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CopilotPage` | #8-style chat custom page | Livewire chat with streaming; conversation sidebar |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('ai.copilot.view-any') && BillingService::hasModule('ai.copilot')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a per-user/per-company rate limiter (RateLimiter / throttle) on copilot message sends in addition to the LlmGateway budget.

---

## Permissions

`ai.copilot.use` (+ every tool checks its own domain permission at call time)

---

## Test Checklist

- [ ] Tenant isolation: tools return current-company data only (per-tool test)
- [ ] Module gating + conversation privacy (other users can't read)
- [ ] Tool denied when user lacks the domain permission (LLM told "not permitted")
- [ ] Unknown/disabled-module tool never offered to the model
- [ ] Prompt-injection fixture: instruction embedded in tool result not followed (system-prompt assertion *(assumed: best-effort test)*)
- [ ] Usage logged per message; budget stop surfaces friendly error
- [ ] Provider mocked

---

## Build Manifest

```
database/migrations/xxxx_create_ai_copilot_conversations_table.php
database/migrations/xxxx_create_ai_copilot_messages_table.php
app/Models/AI/{CopilotConversation,CopilotMessage}.php
app/Data/AI/SendCopilotMessageData.php
app/Support/AI/{CopilotToolRegistry,ToolDefinition}.php
app/Services/AI/CopilotService.php
app/AI/Tools/{CrmMetricsTool,FinanceMetricsTool,HrHeadcountTool,TicketLookupTool,SummariseRecordTool}.php
app/Filament/AI/Pages/CopilotPage.php
app/Livewire/AI/CopilotChat.php
database/factories/AI/CopilotConversationFactory.php
tests/Feature/AI/{CopilotToolScopeTest,CopilotPermissionTest}.php
```

---

## Related

- [[domains/ai/model-config]]
- [[architecture/security]] — prompt injection
- [[domains/ai/workflow-builder]]
