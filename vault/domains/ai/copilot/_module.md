---
domain: ai
module: copilot
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Copilot

Cross-domain AI assistant: natural-language queries over company data, draft generation (emails, descriptions, job postings), and summarisation. Every LLM call routes through [[../model-config/_module|ai.config]]'s `LlmGateway`. Copilot is a **consumer** — it reads other domains only through their services (permission-checked, `CompanyScope`-bound, read-only) and writes only its own conversation/message tables.

## Module-key

| Field | Value |
|---|---|
| key | `ai.copilot` |
| priority | p3 |
| panel | ai |
| permission-prefix | `ai.copilot` |
| tables | `ai_copilot_conversations`, `ai_copilot_messages` |
| encrypted-fields | *(none)* |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../model-config/_module\|ai.config]] | `LlmGateway`: provider, budget, usage metering |
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()`, per-tool permission checks |

Data tools per domain are available only when that module is active **and** the asking user holds the matching permission.

## Core Features

- **Chat console** — ask questions about company data ("how many deals closed this month?"); streaming assistant responses; per-user conversation history.
- **Tool registry** — domains register read-only copilot tools; each wraps an existing service/metric query, runs under `CompanyScope`, and checks the asking user's permission before execution. Tool results are data, never raw SQL access.
- **Draft generation** — email replies, product descriptions, job postings.
- **Summarisation** — documents, ticket threads, meeting notes (content passed in, permission-checked at fetch).
- **Context awareness** — panel/record context passed as structured metadata.
- **Guardrails** ([[security]]) — tools are the only data path (no free-form queries); tool inputs validated; prompt-injection defence: tool results wrapped as data-only content, system prompt instructs distrust of embedded instructions; outputs rendered as text (never executed/HTML).
- Usage metered per message via `LlmGateway`.

## See features/

- [[features/chat-console|Chat Console]] — streaming Livewire chat surface + conversation sidebar.
- [[features/tool-registry|Tool Registry]] — the read-only, permission-guarded data path (no screen).
- [[features/draft-and-summarise|Draft & Summarise]] — draft generation + summarisation modes within the chat surface.

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

## Test Checklist

- [ ] Tenant isolation: tools return current-company data only (per-tool test).
- [ ] Module gating + conversation privacy (other users can't read).
- [ ] Tool denied when user lacks the domain permission (LLM told "not permitted").
- [ ] Unknown/disabled-module tool never offered to the model.
- [ ] Prompt-injection fixture: instruction embedded in tool result not followed (system-prompt assertion *(assumed: best-effort test)*).
- [ ] Usage logged per message; budget stop surfaces a friendly error.
- [ ] Provider mocked.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none)* | — | no domain events |
| Provides | *(nothing)* | — | copilot is a pure consumer; no other domain reads from it |
| Reads | `LlmGateway::complete(feature, messages, opts)` command API | [[../model-config/_module\|ai.config]] | the single metered LLM call path |
| Reads | per-tool metrics/records via domain services (permission-checked, `CompanyScope`-bound, **read-only**) | crm, finance, hr, support | tools NEVER write other domains' tables and NEVER free-form query — the only data path is a registered tool ([[../../../security/data-ownership]]) |

**Data ownership:** `ai.copilot` writes only `ai_copilot_conversations` and `ai_copilot_messages`; cross-domain access is read-only through services, never their tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../model-config/_module|ai.config]] · [[../document-intelligence/_module|Document Intelligence]] · [[../workflow-builder/_module|Workflow Builder]]
- [[../../../architecture/security]] — prompt injection
