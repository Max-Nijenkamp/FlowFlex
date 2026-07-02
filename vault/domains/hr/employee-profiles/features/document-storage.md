---
domain: hr
module: employee-profiles
feature: document-storage
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Document Storage

> Planned vertical slice. Back to [[../_module]].

## Purpose

Store employee documents and profile photo via Media Library (`spatie/laravel-media-library`), surfaced on the employee view page.

## Behavior

- Document types: employment contract, ID documents, certifications.
- Profile photo upload.
- Rendered in the Documents tab of the employee view page (#2 detail with tabs).
- Depends hard on [[../../../core/file-storage/_module|core.files]].

## UI

- **Kind**: simple-resource
- **Page**: "Documents" tab on the Employee view (`/hr/employees/{id}` → Documents tab)
- **Layout**: relation-manager table of uploaded documents (filename, type, uploaded_at, size) with an upload action (Media Library, drag-drop).
- **Key interactions**: upload a document, preview or download an existing one.
- **States**: empty = "No documents" · loading = upload progress bar · error = rejected file type/size (see [[../security]]) · selected = preview / download.
- **Gating**: visible with `hr.employees.view`; upload requires `hr.employees.update`.

## Data

- Owns / writes: Media Library records attached to `hr_employees` — the media table is owned by [[../../../core/file-storage/_module|core.files]]; this module owns the `hr_employees` association only.
- Reads: core.files storage.
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: none.
- Shared entity: reads core.files (Media Library) storage config.

## Related

- Dependency: [[../../../core/file-storage/_module|core.files]]
- Filament: Employee view page → Documents tab — [[../_module]]
- Permissions: `hr.employees.view` / `.update` — [[../security]]
