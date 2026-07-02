---
domain: analytics
module: data-views
feature: view-export
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# View Export

Export a resolved cross-domain view's aggregate data to Excel.

## Behaviour

- Export button on a rendered view produces an Excel file of the current `DataViewResult` (respecting the active date range).
- Large sets are queued (`maatwebsite/laravel-excel`, per [[../../../../architecture/queue-jobs]]).
- The export action is **rate-limited** ([[../../../../architecture/security]]); files are tenant-scoped.

## UI

- **Kind**: widget/action — an export button + toast within [[view-explorer]]; no page of its own.
- **Page**: action on `DataViewsPage`.
- **Layout**: export button in the view header; progress toast; download link on completion.
- **Key interactions**: click export → (large set) queued job → toast "preparing…" → notification + download link when ready; small set → immediate download.
- **States**: idle (button) · loading (queued/"preparing…") · error (generation failed → toast + retry) · done (download link/toast).
- **Gating**: requires `analytics.data-views.export`; throttled per the rate limiter.

## Data

- Owns / writes: nothing persistent (transient export file under the company disk `companies/{id}/exports/`).
- Reads: the resolved `DataViewResult` (already CompanyScope-safe from the source domains).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: the resolved result from [[view-explorer]].
- Feeds: nothing (terminal). Not the same as `analytics.exports` (scheduled/recurring) — this is on-demand.
- Shared entity: none.

## Unknowns

- Excel only vs +PDF — *(assumed Excel)*. See [[../unknowns]].
- Rate-limit threshold — *(assumed)* per [[../../../../architecture/security]].

## Related

- [[../_module|Cross-Domain Data Views]] · [[view-explorer]] · [[../../scheduled-exports/_module|analytics.exports]]
