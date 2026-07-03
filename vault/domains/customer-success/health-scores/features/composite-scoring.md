---
domain: customer-success
module: health-scores
feature: composite-scoring
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Composite Scoring

Combine several weighted signals into a single 0–100 health score per customer account, with a per-factor breakdown for explainability.

## Behaviour

- For each customer account, `HealthScoreService::recalculate` gathers active signals via `SignalRegistry`: support ticket volume, NPS sentiment, payment status (overdue invoices), engagement recency. Product usage is approximated by engagement recency in v1 *(assumed: no usage telemetry)*.
- Each factor produces a normalised value; contribution = value × weight. Weights come from `cs_health_config.factor_weights` and sum to 100.
- When a soft-dep signal source (support / finance / nps) is inactive, its factor is excluded and remaining weights are renormalised to 100.
- Score = sum of contributions, clamped 0–100. Tier is derived from `tier_thresholds`: green ≥70 / amber 40–69 / red <40 *(assumed)*.
- One current row per account (`is_current = true`); prior rows are retained as trend history.
- Recalc is idempotent per run; a per-account failure does not abort the batch.

## UI

- **Kind**: custom-page — the health dashboard is a custom Filament page.
- **Page**: `HealthDashboardPage` at `/crm/health` (Customer Success nav group). `HealthScoreResource` (read-only simple-resource) provides the per-account list.
- **Layout**: tier distribution summary + segmented account list; per-account drill-down shows the factor breakdown table (factor · value · weight · contribution). Configuration form edits `factor_weights` and `tier_thresholds`.
- **Key interactions**: filter/segment by tier; open an account to view its breakdown; save weights → `ConfigureHealthData` → `cs_health_config`. Scores themselves are read-only (computed by the nightly job).
- **States**: empty (no scores yet — recalc has not run) · loading (dashboard aggregate query) · error (weights don't total 100 → rejected) · selected (active tier segment / opened account highlighted).
- **Gating**: `cs.health.view-any` to view; `cs.health.configure` to edit weights/thresholds.

## Data

- Owns / writes: `cs_health_scores`, `cs_health_config` (its own tables only).
- Reads: CRM accounts (`crm.contacts` read API), billing/finance payment status, support ticket metrics, and NPS sentiment — all through each owning domain's read API, **never their tables**.
- Cross-domain writes: none — scoring only writes this module's own tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none as events v1 — signals are pulled on the nightly schedule *(assumed)*.
- Feeds: exposes `HealthScoreService` breakdown/trend as an internal read API to `cs.churn`, `cs.qbr`, `cs.analytics`.
- Shared entity: `crm_accounts` (owned by `crm.contacts`) — read-only reference the score is keyed on.

## Test Checklist

### Unit
- [ ] Weight renormalisation over active signals only; score bounded 0-100; factor contribution = value x weight

### Feature (Pest)
- [ ] `recalculate` upserts one current row per account per run; prior row kept for trend
- [ ] Inactive signal module -> excluded from weights, no query, no error
- [ ] Tenant isolation: scores per company

### Livewire
- [ ] Breakdown view renders per-factor contributions; hidden without the health permission/module

## Unknowns

- Tier thresholds (70 / 40) are assumed defaults.
- `is_current` + historical-rows modelling is assumed.
- Usage-as-engagement-proxy is assumed pending real telemetry.

## Related

- [[../_module|Health Scores]]
- [[./tier-drop-alerts|Tier-Drop Alerts]]
- [[../../churn-risk/_module|cs.churn]]
