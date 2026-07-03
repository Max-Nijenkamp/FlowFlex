---
domain: core
module: audit-log
feature: log-browser
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Log Browser

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

The read-only viewer over `activity_log`: who did what, to which record, when, and from which IP — filterable and, for FlowFlex staff, viewable across all companies.

## Behaviour

- Renders `activity_log` via the `rmsramos/activitylog` Filament resource — **read-only** (no create/edit/delete actions exposed).
- Filterable by domain (`log_name`), action type, user (causer), date range, and subject model.
- In `/app` the resource is company-scoped (`CompanyScope`) — a company sees only its own rows.
- In `/admin` a cross-company view bypasses the tenant scope (`withoutGlobalScope`) under the admin guard only — FlowFlex staff can review activity across all companies.
- `properties` shows before/after for ordinary fields; PII/encrypted field values are absent (field names only) per [[pii-denylist]].

## UI

- **Kind**: simple-resource
- **Page**: `AuditLogResource` — read-only list/view in `/app` (package-provided, configured); plus a cross-company variant in `/admin`.
- **Layout**: table with columns for timestamp, log_name (domain), description, causer (user or "system"), subject, and IP; a filter bar for domain / action / user / date-range / subject; a detail view showing the sanitised `properties` diff.
- **Key interactions**: user opens the log browser → applies filters (e.g. by user + date range) → clicks a row to inspect the before/after properties. Admin staff switch to the `/admin` view to see all companies.
- **States**: empty = no matching log rows (empty-state) · loading = table/filter spinner · error = load failure banner · selected = a log row's detail panel open with the property diff.
- **Gating**: `core.audit.view-any` / `core.audit.view` (+ `BillingService::hasModule('core.audit')`). The `/admin` cross-company view is admin-guard only — never exposed in a company panel ([[../security]]).

## Data

- Owns / writes: nothing — the browser is strictly read-only over `activity_log` (this module's table). No write actions exist.
- Reads: `activity_log` (own table); `users` for causer display (read-only). The `/admin` view reads across companies via scope bypass.
- Cross-domain writes: none — read-only surface ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events) — it reads rows written by `AuditLogger::log` (see [[audit-logger]]).
- Feeds: none.
- Shared entity: subject models are polymorphic references owned by every domain; the browser only displays their type + id, never mutates them.

## Test Checklist

### Unit
- [ ] Rendered `properties` diff omits PII/encrypted field values (field names only) per denylist

### Feature (Pest)
- [ ] `/app` resource is company-scoped: company A cannot see company B's log rows (tenant isolation)
- [ ] `/admin` cross-company view returns rows across companies under the admin guard only; denied to a company user

### Livewire
- [ ] `AuditLogResource` exposes no create/edit/delete actions (read-only); filters (domain / action / user / date-range / subject) narrow the table
- [ ] `canAccess()` denied without `core.audit.view-any` or when `core.audit` module inactive

## Related

- [[../_module|Audit Log]] · [[audit-logger]] · [[pii-denylist]] · [[../security]]
