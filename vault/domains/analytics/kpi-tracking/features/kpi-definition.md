---
domain: analytics
module: kpi-tracking
feature: kpi-definition
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# KPI Definition

Create and manage KPIs: name, category, source (metric or manual), target, unit, period.

## Behaviour

- Create a KPI with a category (revenue/growth/efficiency/customer), a target value + unit, and a period (monthly/quarterly).
- Choose a source: a registered `MetricRegistry` key (module must be active) **or** manual entry.
- Metric key validated against the registry on save; unregistered/inactive-module keys rejected.
- Edit/delete definitions (soft delete).

## UI

- **Kind**: simple-resource — standard CRUD ([[../../../../architecture/patterns/filament-resource-checklist]]).
- **Page**: `KpiResource` at `/analytics/kpis`.
- **Columns**: name, category, source (metric key / "manual"), target, current status badge, period.
- **Form fields**: name, category (select), source toggle → metric-key picker (module-filtered) or "manual", target_value, unit, period.
- **Filters**: category, period, status.
- **Row actions**: edit, delete, "record value" (manual KPIs → [[snapshot-capture]] manual path).
- **States**: empty ("define your first KPI" CTA) · loading (table skeleton) · error (invalid metric key → inline validation) · selected (row → edit).
- **Gating**: view with `analytics.kpis.view-any`; create/edit/delete require `analytics.kpis.manage`.

## Data

- Owns / writes: `bi_kpis` (this module's table).
- Reads: `MetricRegistry::available()` to populate + validate the metric-key picker.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: metric keys from [[../../dashboards/features/metric-registry|MetricRegistry]].
- Feeds: definitions to [[snapshot-capture]] and [[kpi-visualisation]].
- Shared entity: metric keys (owned by source domains, referenced read-only).

## Test Checklist

### Unit
- [ ] Metric key validated against `MetricRegistry` on save; unregistered/inactive keys rejected
- [ ] Manual-source KPI requires no metric key

### Feature (Pest)
- [ ] CRUD persists definitions (soft delete); metric picker offers only active-module keys
- [ ] Company A cannot read/edit company B KPI definitions

### Livewire
- [ ] `KpiResource` form toggles metric-key picker vs manual; invalid key shows inline validation
- [ ] Create/edit/delete denied without `analytics.kpis.manage`

## Unknowns

- Per-KPI status band vs global ±5% — see [[../unknowns]].
- Whether a KPI can have multiple metric sources (composite) — *(assumed: single)*.

## Related

- [[../_module|KPI Tracking]] · [[snapshot-capture]] · [[kpi-visualisation]] · [[threshold-alerts]]
