---
domain: core
module: audit-log
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Audit Log — Security

Parent: [[_module]] · See also [[architecture]] · [[features/pii-denylist]]

## Permissions

`core.audit.view-any` · `core.audit.view`

## Authorization

The `/app` resource gates on:
`canAccess() = Auth::user()->can('core.audit.view-any') && BillingService::hasModule('core.audit')`
per [[../../../architecture/filament-patterns]] #1. The resource is **read-only** — no create/edit/delete actions are exposed. See [[../../../security/authn-authz]].

## Tenancy

Every log row carries `company_id`, force-set from context by `AuditLogger::log` (never client-supplied). Company A's logs are invisible to company B via `CompanyScope`. See [[../../../security/tenancy-isolation]].

## Cross-company admin view

The `/admin` panel exposes a cross-company log view that bypasses the tenant scope (`withoutGlobalScope`). This scope bypass is permitted **here only**, and only under the admin guard (FlowFlex staff) — never in a company panel.

## PII denylist

`properties` must never contain raw values of encrypted/PII fields — field **names** only. `AuditLogger::log` strips PII keys against a denylist *(assumed: per-model `$auditExclude` list)* before writing. See [[features/pii-denylist]], [[../../../security/encryption]] and [[../../../architecture/data-lifecycle]].
