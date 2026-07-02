---
domain: analytics
module: scheduled-exports
feature: schedule-management
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Schedule Management

Create, edit, pause, and resume export schedules; pick source, frequency, recipients, and format.

## Behaviour

- Create a schedule: source (report/dashboard/financial) + `source_id`, frequency (daily/weekly/monthly), `send_at`, recipients (company users, min 1), format (xlsx/pdf).
- `source_id` validated to exist + be owner-accessible; recipients validated against the company user set.
- Pause/resume toggles `is_active`; paused schedules are skipped by the run loop.
- On save, `next_run_at` is computed in the company timezone.

## UI

- **Kind**: simple-resource — CRUD of schedules ([[../../../../architecture/patterns/filament-resource-checklist]]).
- **Page**: `ScheduledExportResource` at `/analytics/exports`.
- **Columns**: source (type + name), frequency, send time, format, recipients count, active toggle, next run.
- **Form fields**: source type + source picker (module-filtered), frequency, send_at, recipients multi-select (company users), format.
- **Filters**: source type, frequency, active/paused.
- **Row actions**: edit, pause/resume, delete, view delivery log ([[delivery-log]]).
- **States**: empty ("schedule your first export" CTA) · loading (table skeleton) · error (inaccessible source / no recipients → inline validation) · selected (row → edit).
- **Gating**: view with `analytics.exports.view-any`; create/edit/pause/resume/delete with `analytics.exports.manage`.

## Data

- Owns / writes: `bi_export_schedules` (this module's table).
- Reads: source existence/accessibility via the source domain's read path; company users for recipient validation.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: report definitions from [[../../report-builder/_module|analytics.reports]]; dashboards from [[../../dashboards/_module|analytics.dashboards]].
- Feeds: due schedules to [[recurring-generation]].
- Shared entity: recipient users (referenced by id).

## Test Checklist

### Unit
- [ ] `next_run_at` computed in the company timezone per frequency
- [ ] Recipients validated: min 1, all company users; `source_id` must exist + be accessible

### Feature (Pest)
- [ ] Pause sets `is_active` false and the run loop skips it; resume recomputes `next_run_at`
- [ ] Company A cannot schedule exports of company B sources

### Livewire
- [ ] Form validates source picker (module-filtered) + recipients; inline errors on inaccessible source
- [ ] Manage actions denied without `analytics.exports.manage`

## Unknowns

- External recipients + attach-vs-link threshold — see [[../unknowns]].

## Related

- [[../_module|Scheduled Exports]] · [[recurring-generation]] · [[delivery-log]]
