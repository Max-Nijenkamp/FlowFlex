---
type: module
domain: AI & Automation
domain-key: ai
panel: ai
module-key: ai.config
status: planned
priority: p3
depends-on: [core.billing, core.rbac]
soft-depends: [ai.copilot, ai.document-intelligence]
fires-events: []
consumes-events: []
patterns: [encryption, money, custom-pages]
tables: [ai_config, ai_usage_log]
permission-prefix: ai.config
encrypted-fields: ["ai_config.api_key"]
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# AI Model Configuration

Configure LLM providers, models, API keys, usage limits, and cost controls for all AI features in FlowFlex. Builds first in `/ai` — every AI feature reads its provider/budget through this module's `LlmGateway`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | copilot / document-intelligence | the consumers |

---

## Core Features

- Provider config: Anthropic Claude (default *(assumed)*), OpenAI, Azure OpenAI
- Model selection per feature (copilot, document intelligence)
- API key management (encrypted; BYO-key v1 *(assumed: platform-key + usage billing later)*)
- Usage limits: monthly token budget per company — **`LlmGateway` hard-stops at budget**, alert at 80%
- Usage tracking: tokens consumed per feature, per user, over time
- Cost estimation based on provider pricing table
- Feature toggles: enable/disable individual AI features
- Fallback model if primary unavailable
- Data residency setting (EU-hosted models for GDPR)

---

## Data Model

### ai_config — id, company_id (indexed) unique, provider, default_model, feature_models (jsonb), 🔐 api_key (encrypted), monthly_token_budget nullable, data_residency (eu/global), enabled_features (jsonb), budget_alerted_at nullable
### ai_usage_log — id, company_id (indexed), feature, user_id, model, tokens_input, tokens_output, cost_cents, occurred_at — append-only, pruned 12 months *(assumed)*

---

## DTOs

### ConfigureAiData — provider (in set), default_model (valid for provider), api_key (verified test call), monthly_token_budget?, data_residency, enabled_features[]

## Services & Actions

- **`LlmGateway::complete(feature, messages, opts): LlmResponse`** — the single LLM call path for ALL AI features: resolves model per feature, enforces budget + feature toggle (`AiBudgetExceededException`, `AiFeatureDisabledException`), logs usage + cost, fallback on provider error
- `UsageReport::byFeature/byUser(period)`
- Budget alert at 80% (once per month)

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `AiConfigPage` | #7 custom page (form) | provider, models, key (write-only), budget, toggles |
| `AiUsageDashboardPage` | #6 dashboard page | tokens + cost charts |

---

## Permissions

`ai.config.manage` · `ai.config.view-usage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] API key ciphertext; verified before save; never re-displayed
- [ ] Budget hard-stop + 80% alert once/month
- [ ] Disabled feature throws before any provider call
- [ ] Usage rows per call with cost (provider mocked)
- [ ] Fallback model on primary failure

---

## Build Manifest

```
database/migrations/xxxx_create_ai_config_table.php
database/migrations/xxxx_create_ai_usage_log_table.php
app/Models/AI/{AiConfig,AiUsageLog}.php
app/Data/AI/ConfigureAiData.php
app/Contracts/AI/LlmGatewayInterface.php
app/Services/AI/LlmGateway.php (+ provider drivers Anthropic/OpenAI)
app/Exceptions/AI/{AiBudgetExceededException,AiFeatureDisabledException}.php
app/Providers/AI/AiServiceProvider.php
app/Filament/AI/Pages/{AiConfigPage,AiUsageDashboardPage}.php
database/factories/AI/AiUsageLogFactory.php
tests/Feature/AI/{LlmGatewayTest,AiBudgetTest}.php
```

---

## Related

- [[domains/ai/copilot]]
- [[domains/ai/document-intelligence]]
- [[product/pricing-model]] — usage-based billing candidate
- [[architecture/patterns/encryption]]
