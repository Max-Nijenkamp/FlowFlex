---
domain: ai
module: model-config
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# AI Model Configuration — Architecture

See also [[_module|ai.config._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/patterns/encryption]], [[../../../architecture/patterns/custom-pages]], [[../../../architecture/patterns/interface-service]].

---

## Services & Actions

- **`LlmGateway::complete(feature, messages, opts): LlmResponse`** — the single LLM call path for ALL AI features. Bound `LlmGatewayInterface → LlmGateway` in `AiServiceProvider`. Responsibilities:
  1. Resolve the model for `feature` (per-feature override → default).
  2. Enforce the feature toggle (`AiFeatureDisabledException`) and the monthly budget (`AiBudgetExceededException`) **before** any provider call.
  3. Call the resolved provider driver (Anthropic / OpenAI / Azure); on provider error, fall back to the configured fallback model.
  4. Log usage + computed cost to `ai_usage_log`; fire the 80% budget alert once per month.
- `UsageReport::byFeature(period)` / `UsageReport::byUser(period)` — aggregation for the dashboard.
- Provider drivers implement a common contract so features are provider-agnostic.

---

## Provider Drivers

| Driver | Notes |
|---|---|
| Anthropic (default *(assumed)*) | Claude models |
| OpenAI | GPT models |
| Azure OpenAI | EU/data-residency deployments |

Each driver maps `messages` + `opts` to the provider API and returns a normalised `LlmResponse` (content, input/output tokens). The gateway is the only caller of drivers.

---

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `AiConfigPage` | #7 custom page (settings form) | [[../../../architecture/patterns/page-blueprints#Wizard]] *(closest — single settings form, not a multi-step wizard; flagged)* | provider, models, key (write-only), budget, toggles, residency |
| `AiUsageDashboardPage` | #6 dashboard page | [[../../../architecture/patterns/page-blueprints#Dashboard]] | tokens + cost charts (by feature / user / time); widget polling 30–60s |

**Access contract (mandatory):** both pages are custom Filament pages and MUST state `canAccess()` explicitly (Filament does not auto-gate custom pages). `AiConfigPage` gates on `Auth::user()->can('ai.config.manage') && BillingService::hasModule('ai.config')`; `AiUsageDashboardPage` gates on `Auth::user()->can('ai.config.view-usage') && BillingService::hasModule('ai.config')` — per [[../../../architecture/filament-patterns]] #1 and [[../../../architecture/patterns/custom-page-checklist]]. No public/portal surface (internal settings only).

Pattern reference: [[../../../architecture/patterns/custom-pages]], [[../../../architecture/ui-strategy]].

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| `ai_config` settings save (`AiConfigPage`) | Optimistic | `updated_at` stale-check → conflict notification ([[../../../architecture/patterns/optimistic-locking]]); one config row per company, but two admins can edit concurrently |
| `ai_usage_log` write (per LLM call) | n/a | Append-only ledger; `LlmGateway` is the sole writer, one insert per call — no concurrent edit of an existing row |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]]. The `budget_alerted_at` once-per-month guard is set inside `LlmGateway` under `CompanyContext`; concurrent calls near the 80% threshold rely on the same row update and are acceptable at-least-once alert semantics *(assumed)*.

---

## Jobs & Scheduling

- `PruneUsageLogCommand` — daily; prunes `ai_usage_log` rows older than 12 months *(assumed)*.
- Budget-alert dispatch is inline in `LlmGateway` (once/month guard via `budget_alerted_at`), not a scheduled job.

No Meilisearch index and no realtime broadcast for this module.
