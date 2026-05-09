---
tags: [flowflex, domain/ai-automation, infrastructure, llm, phase/6]
domain: AI & Automation
panel: ai
color: "#06B6D4"
status: planned
last_updated: 2026-05-08
---

# AI Infrastructure

The engine room behind all FlowFlex AI features. LLM routing, prompt management, cost controls, and privacy-first data handling. Admins control what AI can do and how much it costs.

**Who uses it:** Admins, IT
**Filament Panel:** `ai` (admin section)
**Depends on:** Core
**Phase:** 6
**Build complexity:** High — 2 resources, 3 pages, 4 tables

---

## Features

### LLM Provider Management

Supported providers (configurable per tenant):
- **OpenAI** — GPT-4o, GPT-4o-mini (default)
- **Anthropic** — Claude Sonnet, Claude Haiku
- **Google** — Gemini 1.5 Pro, Gemini Flash
- **Local / Self-hosted** — Ollama (Llama 3.1, Mistral, etc.) for on-premise deployments
- **Azure OpenAI** — enterprise customers with data residency requirements

Model routing strategy:
- Simple queries → fast/cheap model (GPT-4o-mini)
- Complex reasoning → powerful model (GPT-4o / Claude Sonnet)
- Bulk processing → cheapest capable model
- Admin can override routing per feature type

### API Key Management

- Store provider API keys encrypted at rest (AES-256)
- Test key validity before saving
- Rotate keys without downtime
- Per-key usage tracking and budget limits
- Fallback key if primary exhausted

### Cost Management

- Real-time token usage dashboard (daily, weekly, monthly)
- Cost breakdown by: user, feature, model, department
- Budget limits: tenant-level, user-level, feature-level
- Alerts when approaching budget threshold
- Auto-pause AI features when budget exceeded (configurable)
- Monthly cost forecast based on current usage trends
- Token cost per feature benchmarks (so admins can see ROI)

### Prompt Library

- Central store for all system prompts used across FlowFlex features
- Version control — every prompt change tracked
- A/B testing — run two prompt variants, compare output quality ratings
- Admin can edit any system prompt to customise AI behaviour
- Protected prompts (core functionality — read-only for safety)
- Import/export prompt collections

### Privacy & Data Handling

- PII masking before sending to external LLMs (names, emails, phone numbers replaced with tokens)
- Configurable: which fields are masked, masking strategy
- Data residency selector: EU-only routing, US-only, etc.
- Zero-retention mode: request provider to not store queries (where supported)
- AI query audit log with full input/output (stored internally only)
- GDPR: delete AI history on subject access request

### Usage Analytics

- Active AI users (weekly/monthly)
- Most-used AI features
- Average response quality (user thumbs up/down feedback)
- Query category breakdown (HR, Finance, CRM, etc.)
- Time saved estimates (based on action completion time comparisons)
- Token efficiency trends (cost per useful action over time)

---

## Database Tables (4)

### `ai_provider_configs`
| Column | Type | Notes |
|---|---|---|
| `provider` | enum | `openai`, `anthropic`, `google`, `ollama`, `azure` |
| `api_key` | string encrypted | |
| `base_url` | string nullable | for self-hosted |
| `default_model` | string | |
| `fallback_model` | string nullable | |
| `is_active` | boolean | |
| `monthly_budget_eur` | decimal nullable | |
| `current_month_spend_eur` | decimal default 0 | |

### `ai_token_usage`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | |
| `feature_key` | string | e.g. `copilot`, `email_draft`, `agent_run` |
| `model` | string | |
| `tokens_input` | integer | |
| `tokens_output` | integer | |
| `cost_eur` | decimal | |
| `used_at` | timestamp | |

### `ai_prompts`
| Column | Type | Notes |
|---|---|---|
| `key` | string | unique per company or system |
| `name` | string | |
| `content` | text | the prompt |
| `feature_key` | string | which feature uses this |
| `version` | integer | |
| `is_system` | boolean | system prompts vs custom |
| `is_protected` | boolean | |
| `created_by` | ulid FK nullable | |

### `ai_feedback`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | |
| `feature_key` | string | |
| `message_id` | ulid FK nullable | → ai_messages |
| `rating` | enum | `positive`, `negative` |
| `comment` | text nullable | |

---

## Permissions

```
ai.infrastructure.view
ai.infrastructure.manage-providers
ai.infrastructure.manage-budgets
ai.infrastructure.view-usage
ai.infrastructure.manage-prompts
ai.infrastructure.view-audit-log
```

---

## Related

- [[AI Overview]]
- [[AI Assistant & Copilot]]
- [[AI Agents]]
- [[Workflow Automation Builder]]
- [[Security & Compliance]]
