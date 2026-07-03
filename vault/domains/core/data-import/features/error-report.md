---
domain: core
module: data-import
feature: error-report
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Error Report

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

An import **never aborts on a row failure**. Each failing row is captured with its per-column error messages and written to a downloadable CSV.

- Path stored on `data_imports.error_report_path` (tenant-scoped file via `FileStorageService`).
- Counts tracked live: `total_rows`, `success_rows`, `error_rows`.
- The view screen surfaces progress, counts, and the error-report download.
- Only an **infrastructure** failure moves the import to `failed`; row-level errors keep the import running to `complete`.

## UI

- **Kind**: custom-page
- **Page**: Import detail/view — `DataImportResource` view page (`/app/data-imports/{id}`)
- **Layout**: header with target + filename + status badge; a progress section showing `total_rows` / `success_rows` / `error_rows` counters (progress bar while `processing`); a prominent "Download error report" button when `error_report_path` is set.
- **Key interactions**: user opens a finished (or in-flight) import → reads live counts → clicks download to pull the tenant-scoped error CSV of failed rows with per-column messages.
- **States**: empty = import still `pending`, counts zero, no report yet · loading = `processing`, counts tick up, download hidden · error = import `failed` (infra) — failure banner shown · selected = `complete` with `error_rows > 0`, download button active.
- **Gating**: `core.import.view-any` (+ `BillingService::hasModule('core.import')`).

## Data

- Owns / writes: `data_imports` (this module's only table) — `ProcessImportJob` increments `success_rows` / `error_rows`, writes `error_report_path`, and drives `status`.
- Reads: the generated error-report file is fetched read-only from tenant-scoped storage via `FileStorageService` ([[../file-storage/_module]]).
- Cross-domain writes: none — the error report records failures against the import row only, never writes into the target domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: none (no domain events fired).
- Shared entity: the error-report CSV is a tenant-scoped file whose storage path contract is owned by [[../file-storage/_module]]; this feature reads/writes only the path string on its own `data_imports` row.

## Test Checklist

### Unit
- [ ] `success_rows` / `error_rows` counters computed correctly; a row failure increments `error_rows` and does not abort the import

### Feature (Pest)
- [ ] A failing row is recorded in the error-report CSV (tenant-scoped path via `FileStorageService`); the import continues to `complete`
- [ ] An infrastructure failure moves the import to `failed`; row-level errors alone do not
- [ ] Error report and source file land under `companies/{company_id}/` only (tenant isolation)

### Livewire
- [ ] View page shows live counts and a download button only when `error_report_path` is set; `canAccess()` gated on `core.import.view-any` + module active
