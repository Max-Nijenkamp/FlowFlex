---
type: module
domain: AI & Automation
panel: ai
module-key: ai.config
status: planned
color: "#4ADE80"
---

# AI Model Configuration

Configure LLM providers, models, API keys, usage limits, and cost controls for all AI features in FlowFlex.

## Core Features

- Provider config: Anthropic Claude, OpenAI, Azure OpenAI (configurable)
- Model selection per feature (copilot, document intelligence, etc.)
- API key management (encrypted)
- Usage limits: monthly token budget per company, alert when approaching
- Usage tracking: tokens consumed per feature, per user, over time
- Cost estimation based on provider pricing
- Feature toggles: enable/disable individual AI features
- Fallback model if primary is unavailable
- Data residency setting (EU-hosted models for GDPR)

## Data Model

| Table | Key Columns |
|---|---|
| `ai_config` | company_id, provider, default_model, api_key (encrypted), monthly_token_budget, data_residency |
| `ai_usage_log` | company_id, feature, user_id, model, tokens_input, tokens_output, cost_cents, occurred_at |

## Filament

**Nav group:** Settings

- `AiConfigPage` (custom page) — provider, model, key, limits config
- `AiUsageDashboardPage` (custom page) — token usage + cost charts

## Cross-Domain / Security

- API keys encrypted (see [[architecture/patterns/encryption]])
- Usage feeds usage-based billing (see [[product/pricing-model]])

## Related

- [[domains/ai/copilot]]
- [[domains/ai/document-intelligence]]
- [[product/pricing-model]]
