---
domain: hr
module: employee-self-service
feature: my-documents
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# My Documents

**Purpose.** Let the employee view and download their own personal documents (contract, payslips, certifications).

**Behavior.** `MyDocumentsPage` (#1-style list, own scope) surfaces personal docs from the Media Library, scoped to the auth employee only.

**Source module.** [[../../employee-profiles/_module]] (Media Library on the employee record)

**Permissions.** `hr.self-service.view`.

## UI

- **Kind**: custom-page
- **Page**: "My Documents" (`/app/my-documents`)
- **Layout**: list of own personal documents (Media Library) with view/download; read-only — no upload *(assumed: employee cannot upload)*.
- **Key interactions**: browse own documents; preview; download.
- **States**: empty = "No documents"; loading = list skeleton; error = "Could not load"; selected = preview / download.
- **Gating**: visible with `hr.self-service.access`; download own docs only (self-scoped).

## Data

- Owns / writes: none — this module owns no tables.
- Reads: own Media Library documents attached to `hr_employees` (files owned by core.files), scoped to `Auth::user()->employee`.
- Cross-domain writes: none.

## Relations

- Consumes: none.
- Feeds: none.
- Shared entity: reads core.files Media Library (own docs).

[[../_module]]
