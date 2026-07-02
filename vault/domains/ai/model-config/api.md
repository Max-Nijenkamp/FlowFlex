---
domain: ai
module: model-config
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Model Configuration — API / DTOs

The public surface of this module is the **`LlmGateway`** command API (consumed by every other AI feature) plus one settings DTO.

---

## ConfigureAiData (input)

Written by `AiConfigPage`.

| Field | Type | Rules |
|---|---|---|
| `provider` | enum | in: anthropic, openai, azure |
| `default_model` | string | valid for the chosen provider |
| `api_key` | string | verified with a test call before save; encrypted at rest; never returned |
| `feature_models` | array | per-feature model override map (nullable) |
| `monthly_token_budget` | int | nullable, min:0 |
| `data_residency` | enum | in: eu, global |
| `enabled_features` | array | feature keys to enable |

---

## LlmGateway (command API — the single LLM call path)

`LlmGateway::complete(string $feature, array $messages, array $opts = []): LlmResponse`

- **Callers**: `ai.copilot`, `ai.document-intelligence`, and optionally `ai.workflows` — never call a provider directly.
- **Enforced before the provider call**: feature toggle (`AiFeatureDisabledException`) and monthly budget (`AiBudgetExceededException`).
- **Returns** `LlmResponse` { content, tokens_input, tokens_output, model }.
- **Side effect**: writes one `ai_usage_log` row with computed `cost_cents`; fires the 80% budget alert once per month.
- On provider error: retries on the configured fallback model before surfacing an error.

`UsageReport::byFeature(Period)` / `UsageReport::byUser(Period)` — read-only aggregations for the dashboard.

---

## Public / Portal Endpoints

None. Internal `/ai` settings + an in-process service API. No external HTTP routes.
