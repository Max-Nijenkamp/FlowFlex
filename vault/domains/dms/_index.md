---
type: domain-index
domain: Document Management
panel: dms
color: "#4ADE80"
---

# Document Management

Document libraries, version control, approval workflows, retention policies, templates, and wiki pages. **Panel:** `/dms` (Slate) — Phase 2.

**Displaces**: Confluence, Notion (internal docs), SharePoint (SMB tier)

---

## Navigation Groups

- **Documents** — Document Library, Document Viewer
- **Wiki** — Wiki Pages
- **Approvals** — Approval Requests
- **Templates** — Document Templates
- **Settings** — Approval Workflows, Retention Policies, Legal Holds

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/dms/document-library\|Document Library]] | `dms.library` | planned | **P2 core** |
| [[domains/dms/version-control\|Version Control]] | `dms.versions` | planned | P2 |
| [[domains/dms/wiki\|Wiki Pages]] | `dms.wiki` | planned | P2 |
| [[domains/dms/templates\|Document Templates]] | `dms.templates` | planned | P3 |
| [[domains/dms/approval-workflows\|Approval Workflows]] | `dms.approvals` | planned | P3 |
| [[domains/dms/retention-policies\|Retention Policies]] | `dms.retention` | planned | P3 |

---

## Key Patterns

- `spatie/laravel-media-library` — all file storage under `companies/{id}/dms/`
- `spatie/laravel-sluggable` — document + wiki slugs
- `awcodes/filament-tiptap-editor` — wiki pages, templates
- `architecture/search` — full-text document search
- `spatie/laravel-model-states` — approval status
- Custom pages — Document Library (folder tree), Wiki, Document Viewer
- Integrates with Core Data Privacy for GDPR retention
