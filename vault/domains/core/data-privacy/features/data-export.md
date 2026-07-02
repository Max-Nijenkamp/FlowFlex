---
domain: core
module: data-privacy
feature: data-export
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Data Export

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

Full-company and per-subject dataset export.

- `DataExportPage` (`/app`, custom page) triggers a full export, polling ~30s and offering the ZIP when ready.
- `ExportCompanyDataAction::run(): string` builds a full company export (data portability), owner-triggered.
- Per-subject access requests use `ProcessAccessRequestJob` (exports queue): `PersonalDataRegistry::tablesFor($email)` → ZIP of CSVs → `result_path`, then `completed`.
- Export is rate-limited (one per company per N minutes) and owner-only — see [[../security]].

## UI

- **Kind**: custom-page
- **Page**: `DataExportPage` — custom Filament page under `/app` (Settings nav)
- **Layout**: a single "Export company data" action button with an explainer, plus a status/progress area that appears once triggered (polling ~30s) and swaps to a "Download ZIP" link when the file is ready.
- **Key interactions**: owner clicks Export → action dispatches the build → page polls until `result_path`/ZIP is ready → owner downloads. Per-subject access exports are triggered instead from the [[dsar-queue]] Process action (background job), surfacing the download on that request row.
- **States**: empty = no export yet (just the trigger button) · loading = building, poll spinner · error = build/infra failure or rate-limit hit (throttle message) · selected = ready, download link active.
- **Gating**: `core.privacy.export` and owner-only; page states `canAccess()` explicitly (+ `BillingService::hasModule('core.privacy')`).

## Data

- Owns / writes: `dsar_requests` for the per-subject access path (`result_path`, status). The full-company `ExportCompanyDataAction` writes only the ZIP file to tenant-scoped storage and returns its path — it does not write another domain's tables.
- Reads: **many domains, read-only** — `PersonalDataRegistry::tablesFor($email)` resolves registered PII tables/rows and the action reads every registered model's rows to serialise CSVs. All reads only.
- Cross-domain writes: none — export is pure read + file write ([[../../../../security/data-ownership]]). The ZIP is stored via `FileStorageService` ([[../file-storage/_module]]).

## Relations

- Consumes: registrations pushed into `PersonalDataRegistry` by every module's ServiceProvider (declaring their PII tables) — a read-only registry, not an event.
- Feeds: none directly (completion updates the DSAR row consumed by the [[dsar-queue]] notification path).
- Shared entity: the PII table/field definitions are owned by each source domain module and read via the registry; the export ZIP's storage path contract is owned by [[../file-storage/_module]].
