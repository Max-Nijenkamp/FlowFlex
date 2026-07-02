---
domain: analytics
module: report-builder
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Report Builder — Registry, Services & Contracts

No REST API and no events in v1. The "API" is the internal `ReportSourceRegistry` that reporting-enabled domains register against, plus the `ReportRunner`.

---

## ReportSourceRegistry (the cross-domain read contract)

`app/Support/Analytics/ReportSourceRegistry` — a singleton populated at boot.

- `register(string $key, SourceDefinition $def): void` — a domain registers a reportable entity.
- `available(): Collection` — sources filtered by `BillingService::hasModule(...)`.
- `get(string $key): ?SourceDefinition` — resolve one source.

### SourceDefinition
| Field | Type | Notes |
|---|---|---|
| key | string | `{domain}.{entity}` |
| model | class-string | the Eloquent model queried (owning domain's) |
| label | string | shown in the source picker |
| module_key | string | drives the `hasModule` filter |
| whitelisted_columns | array | **only** these are selectable — encrypted/sensitive columns never listed |
| filterable | array | fields allowed in filters |
| aggregatable | array | columns allowed in count/sum/avg/min/max |

The whitelist is the sensitivity boundary: a column absent from `whitelisted_columns` can never appear in a report, run, or export.

---

## Services & Actions

- `ReportRunner::run(Report $r, ?int $limit): Collection` — composes an **Eloquent** query (never raw SQL) over the whitelisted source under `CompanyScope`; applies filters, grouping + SQL aggregations, sorting; caps for preview (100), chunks for export.
- `ExportReportJob` — queued (`exports` queue), chunked Excel/CSV; throttled ([[../../../architecture/security]]).

---

## Events

None fired, none consumed.

See [[data-model]], [[security]], [[./features/source-registry|Source Registry feature]].
