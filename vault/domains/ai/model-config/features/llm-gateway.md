---
domain: ai
module: model-config
feature: llm-gateway
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# LLM Gateway

The single metered LLM call path for every AI feature in FlowFlex. No feature calls a provider SDK directly — they all go through `LlmGateway::complete`, which enforces the toggle + budget, meters usage/cost, and falls back on provider error.

## Behaviour

`LlmGateway::complete(feature, messages, opts): LlmResponse` — per call:

1. Resolve the model for `feature` (per-feature override → default).
2. Enforce the feature toggle → `AiFeatureDisabledException` **before** any provider call.
3. Enforce the monthly token budget → `AiBudgetExceededException` (hard stop, not a warning).
4. Call the resolved provider driver (Anthropic / OpenAI / Azure); on provider error, retry on the configured fallback model.
5. Write one `ai_usage_log` row with computed `cost_cents`; fire the 80% budget alert **once per month** (guarded by `ai_config.budget_alerted_at`).

- Runs under `CompanyContext`, so key resolution + usage logging always bind to the acting company — no side-door around `CompanyScope`.
- `LlmGateway` is the **sole writer** of `ai_usage_log`; callers never write it themselves.

## UI

- **Kind**: background   <!-- in-process service API — no screen -->
- Its effects surface in [[usage-dashboard|Usage Dashboard]] (metered rows) and as friendly errors when a call is refused (budget exceeded / feature disabled).

## Data

- Owns / writes: `ai_usage_log` (append-only); reads `ai_config`. Both this module's own tables ([[../../../../security/data-ownership]]).
- Reads: nothing from other domains directly — the *calling* feature supplies permission-checked content.
- Cross-domain writes: none. Consumers (copilot, document-intelligence, workflows) call this API; they never write `ai_usage_log`.

## Relations

- Provides: `LlmGateway::complete(...)` command API → consumed by [[../../copilot/_module|ai.copilot]], [[../../document-intelligence/_module|ai.document-intelligence]], and optionally [[../../workflow-builder/_module|ai.workflows]].
- Reads: provider/budget/toggles from [[provider-config|Provider Config]]'s `ai_config` row.
- Shared entity: none.

## Unknowns

> [!warning] UNVERIFIED
> Fallback semantics (same-provider-different-model vs cross-provider) and whether a fallback call counts against the same budget are undefined. The `cost_cents` provider pricing table has no defined source/owner/update cadence. See [[../unknowns]].

## Related

- [[../_module|AI Model Configuration]] · [[provider-config|Provider Config]] · [[usage-dashboard|Usage Dashboard]]
- [[../api]] · [[../architecture]] · [[../../../../security/data-ownership]]
