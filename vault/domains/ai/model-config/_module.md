---
domain: ai
module: model-config
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# AI Model Configuration

Configure LLM providers, models, API keys, usage limits, and cost controls for every AI feature in FlowFlex. Builds first in `/ai` — copilot, document-intelligence, and (optionally) workflows all route their LLM calls through this module's `LlmGateway`.

## Module-key

`ai.config`

**Priority:** p3  
**Panel:** ai  
**Permission prefix:** `ai.config`  
**Tables:** `ai_config`, `ai_usage_log`  
**Encrypted fields:** `ai_config.api_key`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing/_module\|Billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|RBAC]] | Permissions, `canAccess()` |
| Soft | [[../copilot/_module\|Copilot]] | the consumer of `LlmGateway` |
| Soft | [[../document-intelligence/_module\|Document Intelligence]] | the consumer of `LlmGateway` |

## Core Features

- **Provider config** — Anthropic Claude (default *(assumed)*), OpenAI, Azure OpenAI; API key management (encrypted; BYO-key v1 *(assumed)*).
- **Model selection per feature** — choose which model each AI feature (copilot, document intelligence) uses; fallback model on primary failure.
- **Usage limits & budget** — monthly token budget per company; `LlmGateway` hard-stops at budget, alerts once at 80%.
- **Usage tracking** — tokens/cost per feature, per user, over time; cost estimation from a provider pricing table.
- **Feature toggles** — enable/disable individual AI features; data-residency setting (EU-hosted models for GDPR).

## See features/

- [[features/provider-config|Provider Config]] — provider/model/key/budget/residency settings form.
- [[features/llm-gateway|LLM Gateway]] — the single metered LLM call path (budget hard-stop, fallback, usage log).
- [[features/usage-dashboard|Usage Dashboard]] — tokens + cost charts per feature/user.

## Build Manifest

```
database/migrations/xxxx_create_ai_config_table.php
database/migrations/xxxx_create_ai_usage_log_table.php
app/Models/AI/{AiConfig,AiUsageLog}.php · database/factories/AI/AiUsageLogFactory.php
app/Data/AI/ConfigureAiData.php
app/Contracts/AI/LlmGatewayInterface.php
app/Services/AI/LlmGateway.php (+ provider drivers Anthropic/OpenAI)
app/Exceptions/AI/{AiBudgetExceededException,AiFeatureDisabledException}.php
app/Providers/AI/AiServiceProvider.php
app/Filament/AI/Pages/{AiConfigPage,AiUsageDashboardPage}.php
catalog ai.config in config/flowflex.php; perms in PermissionSeeder
tests/Feature/AI/{LlmGatewayTest,AiBudgetTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot read company B's `ai_config` row or usage rows.
- [ ] Module gating: config + usage pages hidden when `ai.config` inactive.
- [ ] API key stored ciphertext; verified before save; never re-displayed.
- [ ] Concurrent config save: second writer with a stale `updated_at` gets the conflict notification ([[../../../architecture/patterns/optimistic-locking]]).
- [ ] Budget hard-stop + 80% alert once/month.
- [ ] Disabled feature throws before any provider call.
- [ ] Usage rows written per call with cost (provider mocked); fallback model on primary failure.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none)* | — | no domain events |
| Provides | `LlmGateway::complete(feature, messages, opts)` read/command API | ai.copilot, ai.document-intelligence, ai.workflows (opt) | the single LLM call path; budget + toggle enforced here |
| Reads | active-module set | core.billing | feature toggles gate on `hasModule` |

**Data ownership:** `ai.config` writes only `ai_config` and `ai_usage_log`. Consumers never write these tables — they call `LlmGateway`, which writes the usage log itself ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../copilot/_module|Copilot]] · [[../document-intelligence/_module|Document Intelligence]] · [[../workflow-builder/_module|Workflow Builder]]
- [[../../../product/pricing-model]] — usage-based billing candidate
