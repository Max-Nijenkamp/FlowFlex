---
domain: analytics
module: kpi-tracking
feature: snapshot-capture
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Snapshot Capture

The scheduled job that freezes each KPI's actual for the period, plus the manual-entry path.

## Behaviour

- `CaptureKpiSnapshotsCommand` runs monthly (1st) / quarterly.
- For each KPI: metric-sourced → resolve via `MetricRegistry` under the owning domain's `CompanyContext`; manual → skip until a value is entered.
- Upsert one `bi_kpi_snapshots` row per `(kpi, period_label)` — idempotent, safe to re-run.
- After capture, evaluate status and dispatch a below-threshold alert once ([[threshold-alerts]]).
- Manual entry (`RecordManualValueData`) writes the actual for a manual KPI's period.

## UI

- **Kind**: background — the scheduled capture command has no UI. Manual entry is a small action/modal on [[kpi-definition]]'s resource ("record value").
- **Page**: none for the job; manual entry = modal on `KpiResource`.
- **Layout**: manual-entry modal (period selector + actual value).
- **Key interactions**: (job) runs on schedule; (manual) open modal → enter actual → save → snapshot upserted.
- **States**: manual modal — idle · saving · error (validation) · saved.
- **Gating**: manual entry requires `analytics.kpis.record-values`; the job runs system-side under each company's context.

## Data

- Owns / writes: `bi_kpi_snapshots` (upsert per period), reads `bi_kpis`.
- Reads: `MetricRegistry` closures for metric-sourced actuals.
- Cross-domain writes: none; alert dispatch is a service call to notifications ([[../../../../security/data-ownership]]).

## Relations

- Consumes: KPI definitions from [[kpi-definition]]; metric actuals from [[../../dashboards/features/metric-registry|MetricRegistry]].
- Feeds: snapshots to [[kpi-visualisation]]; breach signal to [[threshold-alerts]].
- Shared entity: metric keys (read-only).

## Test Checklist

### Unit
- [ ] Period label derivation (monthly 1st / quarterly) correct across year boundary

### Feature (Pest)
- [ ] Capture upserts one snapshot per `(kpi, period_label)` — re-run is idempotent
- [ ] Metric-sourced actual resolves under the owning domain's `CompanyContext`; manual KPI skipped until entered
- [ ] Manual entry writes the actual for the chosen period; requires `analytics.kpis.record-values`

### Livewire
- [ ] Record-value modal validates period + value; denied without permission

## Unknowns

- Missing-manual-value nudge — see [[../unknowns]].
- Behaviour when a metric-sourced KPI's module is deactivated mid-period — *(assumed: skipped)*.

## Related

- [[../_module|KPI Tracking]] · [[kpi-definition]] · [[threshold-alerts]] · [[../../../../architecture/queue-jobs]]
