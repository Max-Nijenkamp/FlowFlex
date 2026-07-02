---
domain: analytics
module: scheduled-exports
feature: delivery-log
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Delivery Log

The history of each schedule's runs — success/failure, timestamp, file, and error — pruned after 90 days.

## Behaviour

- Every run appends a `bi_export_log` row (status, generated_at, file_path or error).
- Viewable as a relation under a schedule; failures show the error reason.
- `PruneExportLogCommand` (daily) removes rows older than 90 days *(assumed)*.

## UI

- **Kind**: simple-resource (relation) — a read-only log table under [[schedule-management]]'s resource; not a standalone page.
- **Page**: delivery-log relation on `ScheduledExportResource` (+ a "view log" row action).
- **Columns**: generated_at, status badge, format, file link (if success), error (if failed).
- **Key interactions**: open schedule → log tab; click a successful row → download the tenant-scoped file (signed link if large); read failure error.
- **States**: empty (never run yet → "no deliveries yet") · loading (skeleton rows) · error (log load fails → retry) · selected (row → file download / error detail).
- **Gating**: view with `analytics.exports.view-any`.

## Data

- Owns / writes: `bi_export_log` (written by [[recurring-generation]]; read-only here).
- Reads: own log rows; file access via the tenant-scoped company disk.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: log rows produced by [[recurring-generation]].
- Feeds: nothing downstream (terminal history).
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Prune date guard removes only rows older than 90 days

### Feature (Pest)
- [ ] Each run appends one log row (status, file_path or error); rows never updated
- [ ] File download is tenant-scoped — company A cannot fetch company B export files

### Livewire
- [ ] Log relation renders status badges + file links; failure rows show the error reason
- [ ] Denied without `analytics.exports.view-any`

## Unknowns

- Retention window + whether failures notify the owner — see [[../unknowns]].

## Related

- [[../_module|Scheduled Exports]] · [[schedule-management]] · [[recurring-generation]]
