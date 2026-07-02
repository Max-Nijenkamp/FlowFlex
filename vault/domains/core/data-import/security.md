---
domain: core
module: data-import
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Data Import — Security

Parent: [[_module]] · See also [[architecture]] · [[api]]

Security notes per `build/security-audit-2026-06-11`.

## Permissions

`core.import.view-any` · `core.import.create`

## Authorization

`DataImportResource` gates on:
`canAccess() = Auth::user()->can('core.import.view-any') && BillingService::hasModule('core.import')`
per [[../../../architecture/filament-patterns]] #1. See [[../../../security/authn-authz]].

## Module gating

The import target list is built from `ImporterRegistry::available()`, which filters by `hasModule` — targets whose module is inactive are excluded, and the resource itself is module-gated.

## Rate limiter (medium)

The import-create surface carries a low-rate `import` throttle limiter, cited on the Filament/action that creates an import, to bound bulk-upload abuse.

## Upload path contract (medium)

The uploaded file is stored via `FileStorageService` under `companies/{company_id}/` — **no raw `Storage::put`** — matching the file-storage path contract ([[../file-storage/_module]]). The generated error report is likewise a tenant-scoped file (`error_report_path`).

## Tenancy

`data_imports` rows and both the source file and error report are company-scoped; imported rows land under the importing company only. See [[../../../security/tenancy-isolation]].
