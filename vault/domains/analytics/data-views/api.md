---
domain: analytics
module: data-views
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Cross-Domain Data Views — Registry, Services & Contracts

No REST API and no events in v1. The "API" is the internal `DataViewRegistry` + `DataViewContract` that FlowFlex-shipped views implement.

---

## DataViewRegistry

`app/Support/Analytics/DataViewRegistry` — a singleton listing shipped view classes.

- `register(class-string $view): void` — register a shipped `DataView`.
- `available(): Collection` — views whose **every** `requiredModules()` entry passes `BillingService::hasModule(...)`.
- `get(string $key): ?DataViewContract` — resolve one view.

## DataViewContract

| Method | Signature | Notes |
|---|---|---|
| requiredModules | `(): array` | module keys, all must be active |
| run | `(DateRange $range): DataViewResult` | reads each source domain via its own read path under `CompanyContext`; aggregates |
| drillDown | `(array $key, DateRange $range): DataViewResult` | underlying records for one aggregate |

The `run()` / `drillDown()` bodies are the **only** cross-domain data access, and they call each owning domain's read path — so the data-ownership boundary ([[../../../security/data-ownership]]) holds by construction.

---

## Services & Actions

- `DataViewRegistry::available()` — module-filtered listing for the gallery.
- Cached per `(view, range)` at `company:{id}:bi:view:{view}:{range}`, TTL 1 h *(assumed)*.
- Export routed through `maatwebsite/laravel-excel`, throttled per [[../../../architecture/security]].

---

## Events

None fired, none consumed.

See [[data-model]], [[security]], [[./features/view-registry|View Registry feature]].
