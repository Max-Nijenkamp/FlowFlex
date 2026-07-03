---
domain: dms
module: document-library
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Document Library

Folder-based document storage with full-text search, in-browser preview, and inherited folder-level access control. The core repository of the DMS domain — every other DMS module layers on documents. Build first in `/dms`.

## Module-key

`dms.library`

**Priority:** p2  
**Panel:** dms  
**Permission prefix:** `dms.library`  
**Tables:** `dms_folders`, `dms_folder_access`, `dms_documents`, `dms_favourites`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()`, folder-access roles |
| Hard | [[../../core/file-storage/_module\|core.files]] | Media Library; files under `companies/{id}/dms/` via `CompanyPathGenerator` |
| Soft | [[../version-control/_module\|dms.versions]] | Version history layered on documents |
| Soft | [[../approval-workflows/_module\|dms.approvals]] | Approval chains before publication |
| Soft | [[../retention-policies/_module\|dms.retention]] | Lifecycle archive/delete |

## Core Features

- **Folder tree** — nested folders (`parent_folder_id` self-referential, cycle-checked), unique `(parent_folder_id, name)` *(assumed)*.
- **Document record** — name, folder, file (Media Library), description, tags, owner, size, mime, last-modified.
- **Upload** — any allowed file type ([[../../../architecture/security]] upload whitelist + max size); preview PDFs + images in-browser (Office docs: download only, v1 *(assumed)*).
- **Full-text search** — across document names + extracted text; results **post-filtered by folder access**.
- **Folder-level access control** — restrict folders to roles or users, **inherited down the subtree**; restricted folders invisible in list, search, AND direct viewer URL. Resolved by a single `accessibleFoldersFor()` scope.
- **Move / copy** — between folders; access re-checked at the target.
- **Favourite / star** documents; recent-documents view.
- Storage path always `companies/{company_id}/dms/`.

## See features/

- [[features/folder-browser|Folder Browser]] — the tree + document-grid custom page.
- [[features/document-viewer|Document Viewer]] — preview + metadata custom page.
- [[features/document-upload|Document Upload]] — upload + text-extraction pipeline.
- [[features/folder-access-control|Folder Access Control]] — inherited restriction scope.
- [[features/document-search|Document Search]] — access-filtered Meilisearch.

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

## Test Checklist

- [ ] Tenant isolation: company A cannot list, open, search, or download company B's folders/documents.
- [ ] Module gating: artifacts hidden when `dms.library` inactive.
- [ ] Restricted folder invisible in tree, grid, search, and direct viewer URL for a non-permitted user.
- [ ] Access inheritance down a subtree.
- [ ] Upload rejects disallowed types; path under `companies/{id}/dms/`.
- [ ] Folder cycle rejected.
- [ ] Move into an inaccessible folder rejected.
- [ ] PDF text extracted + searchable.
- [ ] Preview uses a temp signed URL.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Commands | `core.files` Media Library + `CompanyPathGenerator` | core.files | All bytes stored under `companies/{id}/dms/` via the file-storage service |
| Fires | *(none)* | — | No cross-domain events; DMS is a read-side consumer surface *(assumed)* |
| Soft-consumed by | HR / CRM as merge sources | dms.templates → hr.profiles, crm.contacts | Templates read field providers; library itself has no direct HR/CRM edge |

**Data ownership:** `dms.library` writes only `dms_folders`, `dms_folder_access`, `dms_documents`, `dms_favourites`. Uploaded bytes are stored through the `core.files` service (its owning API), never by writing another domain's tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../version-control/_module|Version Control]] · [[../approval-workflows/_module|Approval Workflows]] · [[../retention-policies/_module|Retention Policies]] · [[../templates/_module|Templates]]
- [[../../core/file-storage/_module|core.files]] · [[../../../architecture/search]]
