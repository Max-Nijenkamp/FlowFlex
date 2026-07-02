---
domain: ai
module: model-config
feature: usage-dashboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Usage Dashboard

Read-only charts of token consumption and cost, broken down by feature, by user, and over time — so a company can see where its AI spend goes and how close it is to the monthly budget.

## Behaviour

- Aggregates the append-only `ai_usage_log` via `UsageReport::byFeature(period)` and `UsageReport::byUser(period)`.
- Shows tokens (input/output) and `cost_cents` (rendered as money via brick/money) per feature, per user, and as a time series.
- Surfaces budget headroom: current-month spend vs `monthly_token_budget`, with the 80%-alert threshold marked.
- Purely read — writes nothing.

## UI

- **Kind**: custom-page   <!-- #6 dashboard page -->
- **Page**: "AI Usage" (`/ai` → Settings → Usage) *(route slug assumed)*
- **Layout**: header stat cards (this-month tokens, this-month cost, % of budget) + charts (cost over time, cost by feature, top users) + a recent-calls table.
- **Key interactions**: period filter (this month / last 30d / custom); toggle by-feature vs by-user breakdown; hover chart segment → tooltip with tokens + cost.
- **States**: empty (no usage yet → "no AI usage recorded this period") · loading (skeleton cards + charts) · error (toast + retry) · selected (chart segment highlighted, tooltip open).
- **Gating**: `ai.config.view-usage`.

## Data

- Owns / writes: nothing — read-only over `ai_usage_log` (this module's own table).
- Reads: `ai_usage_log` + `ai_config.monthly_token_budget` for the headroom gauge.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: usage rows written by [[llm-gateway|LLM Gateway]].
- Shared entity: `user_id` on usage rows resolves against the platform `users` table (read-only) for the by-user breakdown.

## Unknowns

> [!warning] UNVERIFIED
> Whether cost figures are trustworthy depends on the (undefined) provider pricing table behind `cost_cents`. `ai_usage_log` is pruned at 12 months *(assumed)*, which bounds the time-series depth. See [[../unknowns]].

## Related

- [[../_module|AI Model Configuration]] · [[llm-gateway|LLM Gateway]] · [[provider-config|Provider Config]]
- [[../../../../product/pricing-model]] — usage-based billing candidate
