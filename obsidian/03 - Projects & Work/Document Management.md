---
tags: [flowflex, domain/projects, documents, files, phase/2]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: planned
last_updated: 2026-05-06
---

# Document Management

Centralised file storage for the entire organisation. Organised, versioned, permissioned, searchable.

**Who uses it:** All employees
**Filament Panel:** `projects`
**Depends on:** Core (file storage abstraction — AWS S3 / Cloudflare R2)
**Phase:** 2
**Build complexity:** High — 2 resources, 1 page, 4 tables

## Events Fired

- `DocumentUploaded`
- `DocumentVersioned`
- `DocumentShared`

## Sub-modules

### File Storage & Organisation

- Folder structure (nested, unlimited depth)
- Context folders (project folders, client folders, department folders auto-created)
- File upload (drag and drop, multi-file, up to 5GB per file on Enterprise)
- Supported types: PDF, Word, Excel, PowerPoint, images, video, audio, ZIP, CAD
- Storage backend: AWS S3 or Cloudflare R2 (configurable)
- File tagging and metadata
- Duplicate detection on upload
- File move and copy

### Version History

- Every upload of the same filename creates a new version (not overwrite)
- Version list with uploader name, timestamp, file size
- Restore any previous version
- Compare versions (for text documents where diffing is possible)
- Version retention policy (keep last N versions, auto-delete older)

### Permissions

- Folder and file permissions: view / edit / download / delete — per user or role
- Public sharing links (time-limited, password-optional, download-only option)
- Share with external people (email invite to view without a FlowFlex account)
- Workspace-wide permissions (some folders accessible to all employees)

### Search & Preview

- Full-text search across all file names and document content
- OCR on scanned PDFs (makes scanned documents searchable)
- In-browser preview (PDF, images, video, Office files via OnlyOffice or Google Viewer)
- Recent files list
- Starred/pinned files

### Cloud Sync

- Google Drive folder sync (two-way sync for selected Drive folders)
- Microsoft OneDrive sync (two-way sync)
- Files modified in Google/OneDrive appear in FlowFlex and vice versa
- Sync conflict resolution

## Security Notes

- Never expose raw S3 URLs — always use signed temporary URLs. See [[Security Rules]].
- File permissions checked before URL is generated
- All file metadata is tenant-scoped via `tenant_id`

## Database Tables (4)

1. `folders` — nested folder structure
2. `files` — file metadata (name, path, size, mime type, uploader)
3. `file_versions` — version records per file
4. `file_shares` — sharing links and external share records

## Related

- [[Projects Overview]]
- [[Document Approvals & E-Sign]]
- [[Knowledge Base & Wiki]]
- [[Security Rules]]
