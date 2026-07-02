---
domain: dms
module: document-library
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Library — Decisions

## ADR: Single `accessibleFoldersFor()` scope for all access paths

- **Context:** Folder restrictions must hold across the tree view, the document grid, search results, and the direct viewer URL. Four independent checks would drift.
- **Decision:** One `DocumentService::accessibleFoldersFor(User): Builder` API resolves inheritance once; every list / search / viewer path composes on it.
- **Consequences:** No path can leak a restricted folder without also breaking the others; single point to test. Search must post-filter on the accessible set rather than trusting Meilisearch alone.

## ADR: Storage always under `companies/{id}/dms/`

- **Decision:** All bytes go through `core.files` `CompanyPathGenerator` with a `dms/` prefix; DMS never writes storage paths itself.
- **Consequences:** Tenant isolation compounds through the owning file service; no side-door around company scope ([[../../../security/data-ownership]]).

## ADR: PDF-only text extraction for v1 *(assumed)*

- **Context:** Full-text search needs extracted text; extracting every format is heavy.
- **Decision:** `ExtractDocumentTextJob` extracts PDF text (`pdftotext`) only for v1; other types are searchable by name/metadata.
- **Consequences:** Office-document full-text is a follow-up; noted in [[unknowns]].

## ADR: Office docs preview by download, in-browser preview PDF + images only *(assumed)*

- **Decision:** In-browser preview covers PDF + images; Office formats download for v1.
- **Consequences:** No embedded Office viewer dependency; revisit if users demand inline preview.
