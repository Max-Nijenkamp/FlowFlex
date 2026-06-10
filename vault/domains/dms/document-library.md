---
type: module
domain: Document Management
domain-key: dms
panel: dms
module-key: dms.library
status: planned
priority: p2
depends-on: [core.billing, core.rbac, core.files]
soft-depends: [dms.versions, dms.approvals, dms.retention]
fires-events: []
consumes-events: []
patterns: [custom-pages, search]
tables: [dms_folders, dms_folder_access, dms_documents]
permission-prefix: dms.library
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Document Library

Folder-based document storage with search, preview, and access control. The core repository of the DMS domain — build first in `/dms`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, files under `companies/{id}/dms/` |
| Soft | [[domains/dms/version-control\|dms.versions]], [[domains/dms/approval-workflows\|dms.approvals]], [[domains/dms/retention-policies\|dms.retention]] | layered on documents |

---

## Core Features

- Folder tree: nested folders (parent_folder_id self-referential, cycle-checked)
- Document record: name, folder, file (Media Library), description, tags, owner
- Upload any allowed file type ([[architecture/security]] upload rules); preview PDFs + images in-browser (Office docs: download v1 *(assumed)*)
- Full-text search across document names + extracted text (PDF text extraction job *(assumed: pdftotext in extraction job; other types name-only)*)
- Folder-level access control: restrict folders to roles or users — **inherited down the tree**; restricted folders invisible (list + search + viewer)
- Document metadata: size, type, uploaded by, last modified
- Move/copy documents between folders (access re-checked at target)
- Favourite/star documents; recent documents view
- Storage path always `companies/{company_id}/dms/`

---

## Data Model

### dms_folders

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | unique `(parent_folder_id, name)` *(assumed)* |
| parent_folder_id | ulid nullable FK self | cycle-checked |
| owner_id | ulid FK users | |
| access_level | string default `all` | all / restricted |
| deleted_at | timestamp nullable | |

### dms_folder_access — id, folder_id FK, company_id, role_id nullable, user_id nullable (exactly one set)

### dms_documents

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), folder_id FK | ulid | |
| name | string | |
| slug | string | sluggable, unique per company |
| description | text nullable | |
| owner_id | ulid FK users | |
| file_size | bigint | bytes |
| mime_type | string | |
| extracted_text | text nullable | search source |
| is_archived | boolean default false | retention |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, folder_id)`, `(company_id, owner_id)`

Favourites: polymorphic user-favourites table *(assumed: `dms_favourites` user_id+document_id unique)*.

---

## DTOs

### UploadDocumentData — folder_id (accessible), file (allowed types/size per security rules), name? (default filename), description?, tags[]
### MoveDocumentData — document_id, target_folder_id (accessible, ≠ current)

## Services & Actions

Interface→Service: `DocumentServiceInterface` → `DocumentService`.

- `upload(UploadDocumentData $data): DocumentData` — media via CompanyPathGenerator (`dms/` prefix); dispatches text-extraction job
- `move` / `copy` — target access check
- `accessibleFoldersFor(User $user): Builder` — **the single access-scope API**; every list/search/view path uses it (inheritance resolved)
- `ExtractDocumentTextJob` — `default` queue, pdf only v1, updates `extracted_text` + reindexes

---

## Filament

**Nav group:** Documents

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DocumentLibraryPage` | #11 tree custom page | folder tree sidebar + document grid; drag-drop upload |
| `DocumentViewerPage` | #2-style custom page | PDF/image preview (temp signed URL), metadata, versions (soft-dep) |
| `FolderResource` | #1 CRUD resource | access config |

---

## Permissions

`dms.library.view-any` · `dms.library.upload` · `dms.library.move` · `dms.library.delete` · `dms.library.manage-folders` · `dms.library.manage-access`

(Folder access list is a second gate on top.)

---

## Search & Realtime

Meilisearch: name, description, extracted_text — **results post-filtered by folder access** (folder_id filterable attribute + accessible-set filter). No realtime.

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Restricted folder invisible in tree, grid, search, and direct viewer URL for non-permitted user
- [ ] Access inheritance down subtree
- [ ] Upload rejects disallowed types; path under `companies/{id}/dms/`
- [ ] Folder cycle rejected
- [ ] Move into inaccessible folder rejected
- [ ] PDF text extracted + searchable
- [ ] Preview uses temp signed URL

---

## Build Manifest

```
database/migrations/xxxx_create_dms_folders_table.php
database/migrations/xxxx_create_dms_folder_access_table.php
database/migrations/xxxx_create_dms_documents_table.php
database/migrations/xxxx_create_dms_favourites_table.php
app/Models/DMS/{Folder,FolderAccess,Document,Favourite}.php
app/Data/DMS/{UploadDocumentData,MoveDocumentData,DocumentData}.php
app/Contracts/DMS/DocumentServiceInterface.php
app/Services/DMS/DocumentService.php
app/Providers/DMS/DmsServiceProvider.php
app/Jobs/DMS/ExtractDocumentTextJob.php
app/Filament/DMS/Pages/{DocumentLibraryPage,DocumentViewerPage}.php
app/Filament/DMS/Resources/FolderResource.php
database/factories/DMS/{FolderFactory,DocumentFactory}.php
tests/Feature/DMS/{DocumentLibraryTest,FolderAccessTest,DocumentSearchTest}.php
```

---

## Related

- [[domains/dms/version-control]]
- [[domains/dms/approval-workflows]]
- [[architecture/search]]
- [[domains/core/file-storage]]
