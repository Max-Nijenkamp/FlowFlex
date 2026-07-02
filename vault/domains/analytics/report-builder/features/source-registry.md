---
domain: analytics
module: report-builder
feature: source-registry
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Source Registry

The whitelist of reportable entities: each domain registers a source with only its non-sensitive columns exposed.

## Behaviour

- A domain calls `ReportSourceRegistry::register($key, SourceDefinition)` in its provider, declaring the model, whitelisted columns, filterable + aggregatable fields.
- Encrypted/sensitive columns are never included in `whitelisted_columns` — they cannot be reported.
- `available()` filters sources by `BillingService::hasModule(...)`; inactive modules disappear from the source picker.
- The registry is the only entity/column vocabulary the composer and runner can use.

## UI

- **Kind**: background — no page. In-memory registry populated at boot; surfaced by [[report-composer]]'s source picker.
- **Page**: none.
- **Layout**: n/a.
- **Key interactions**: n/a (registration in code).
- **States**: n/a.
- **Gating**: sources exposed only for active modules; the column whitelist is enforced regardless of permission.

## Data

- Owns / writes: nothing — in-memory registry (the persisted artifact is `bi_reports.data_source` referencing a key).
- Reads: nothing itself; the runner reads the registered model's whitelisted columns at run time.
- Cross-domain writes: none, ever ([[../../../../security/data-ownership]]).

## Relations

- Consumes: registration from every reporting-enabled domain; `hasModule` from [[../../../core/billing-engine/_module|core.billing]].
- Feeds: source + column vocabulary to [[report-composer]] and [[report-runner]].
- Shared entity: none persisted; source keys are the shared vocabulary.

## Test Checklist

### Unit
- [ ] `register($key, SourceDefinition)` stores entity + `whitelisted_columns` + filterable/aggregatable sets.
- [ ] An encrypted/sensitive column can never be added to `whitelisted_columns` (guarded, not just convention).
- [ ] `available()` filters out sources whose owning module returns false from `hasModule(...)`.

### Feature (Pest)
- [ ] A registered source resolves under the owning domain's `CompanyScope`; the runner reads only whitelisted columns.
- [ ] Deactivating a module removes its source from the picker and blocks running saved reports on it.
- [ ] Requesting an unregistered source key fails cleanly (no arbitrary table access).

## Unknowns

- Which domains/entities ship as sources in v1 — *(assumed)* the CRUD-heavy domains (CRM, Finance, HR, Projects). See [[../unknowns]].

## Related

- [[../_module|Report Builder]] · [[report-composer]] · [[../../dashboards/features/metric-registry|MetricRegistry (sibling pattern)]]
