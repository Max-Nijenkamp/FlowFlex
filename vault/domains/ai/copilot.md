---
type: module
domain: AI & Automation
panel: ai
module-key: ai.copilot
status: planned
color: "#4ADE80"
---

# AI Copilot

Cross-domain AI assistant. Natural-language queries over company data, draft generation (emails, descriptions), and summarisation. Powered by an LLM provider.

## Core Features

- Chat interface: ask questions about company data ("how many deals closed this month?")
- LLM provider integration (Anthropic Claude / OpenAI — configurable)
- Tool-use: copilot queries domain data via scoped, read-only tools (always CompanyScope-bound)
- Draft generation: write email replies, product descriptions, job postings
- Summarisation: summarise long documents, ticket threads, meeting notes
- Context awareness: knows which panel/record the user is viewing
- Guardrails: only accesses current company's data, respects user permissions
- Usage tracking (token consumption for billing — usage-based pricing candidate)

## Data Model

| Table | Key Columns |
|---|---|
| `ai_copilot_conversations` | company_id, user_id, title, created_at |
| `ai_copilot_messages` | conversation_id, company_id, role (user/assistant), content, tokens_used |
| `ai_copilot_config` | company_id, provider, model, api_key (encrypted), enabled_tools (json) |

## Filament

**Nav group:** Copilot

- `CopilotPage` (custom page) — chat interface (Vue component, streaming responses)
- `CopilotConfigPage` (custom page) — provider + model config (admin)

## Cross-Domain / Security

- LLM API key encrypted (see [[architecture/patterns/encryption]])
- Data access tools ALWAYS enforce CompanyScope + user permissions — never expose cross-tenant data
- See [[architecture/security]] — prompt injection considerations
- Token usage metered for usage-based billing (see [[product/pricing-model]])

## Related

- [[architecture/security]]
- [[architecture/patterns/encryption]]
- [[domains/ai/workflow-builder]]
