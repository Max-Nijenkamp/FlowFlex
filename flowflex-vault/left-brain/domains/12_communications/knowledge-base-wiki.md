---
type: module
domain: Communications & Internal Comms
panel: comms
phase: 5
status: planned
cssclasses: domain-comms
migration_range: 551000–551499
last_updated: 2026-05-09
---

# Knowledge Base & Wiki

Internal knowledge management. SOPs, policies, how-tos, team documentation. Notion-like editor, version history, and powerful search. Reduces "how do I do X?" Slack messages.

---

## Content Structure

```
Spaces (top-level: Engineering / HR / Finance / Product)
└── Sections (groups of related docs)
    └── Pages (individual documents)
        └── Sub-pages
```

Pages are rich documents: headers, paragraphs, images, code blocks, tables, callouts, embedded videos, database views.

---

## Editor

Block-based editor (Notion-style):
- Type `/` to insert any block type
- Drag blocks to reorder
- @mention a person or page → inline preview
- Embeds: YouTube, Figma, GitHub Gist, Airtable, etc.
- Templates: HR announcement, meeting notes, project brief, post-mortem

---

## Search

Full-text search across all pages:
- Fuzzy matching: "onboarding chekklist" finds "Onboarding Checklist"
- Search within a space
- Recent + starred pages
- AI search: "How do I request a holiday?" → surfaces relevant HR policy

---

## Permissions

| Level | Access |
|---|---|
| Company-wide | Everyone can read |
| Team space | Team members only |
| Private | Creator only |
| Guest | External invited viewer |

Page-level overrides for specific sensitive docs.

---

## Version History

Every page edit creates a version:
- View history of changes
- Restore to any previous version
- See who changed what (diff view)

---

## IT Helpdesk Integration

When employee submits IT ticket, system suggests relevant KB articles:
- AI matches ticket subject to KB content
- Deflects tickets before submission if self-service answer found

---

## Data Model

### `comms_kb_spaces`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| icon | varchar(100) | nullable |
| visibility | enum | public/team/private |

### `comms_kb_pages`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| space_id | ulid | FK |
| parent_id | ulid | nullable self-FK |
| title | varchar(500) | |
| content | longtext | JSON block document |
| author_id | ulid | FK |
| last_edited_by | ulid | FK |
| sort_order | int | |

---

## Migration

```
551000_create_comms_kb_spaces_table
551001_create_comms_kb_pages_table
551002_create_comms_kb_page_versions_table
```

---

## Related

- [[MOC_Communications]]
- [[team-messaging]]
- [[company-announcements]]
- [[MOC_IT]] — helpdesk deflection
