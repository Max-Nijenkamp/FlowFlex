---
type: module
domain: Projects & Work
panel: projects
module-key: projects.wikis
status: planned
color: "#4ADE80"
---

# Wikis

> Project-scoped wiki pages — rich text content, internal linking, page hierarchy, full-text search, and version history.

**Panel:** `projects`
**Module key:** `projects.wikis`

## What It Does

Wikis gives each project its own internal knowledge base. Team members create pages with rich-text content — onboarding docs, process guides, architecture decisions, meeting notes, runbooks. Pages are organised in a hierarchy (parent page → child pages). Internal links between wiki pages keep knowledge connected. Every page retains its full edit history so changes can be reviewed and reverted. Full-text search across all wiki pages in a project makes knowledge discoverable. Company-level wikis (not project-scoped) are managed separately as a top-level section.

## Features

### Core
- Wiki pages: title, rich-text body (headings, lists, code blocks, tables, images, embeds), author, last edited
- Page hierarchy: pages can have parent pages creating a nested sidebar tree
- Internal links: `[[Page Title]]` syntax creates links between wiki pages (resolved at render time)
- Full-text search: search within the current project's wiki — results show page title and matched excerpt
- Page history: every save creates a revision — view, compare, and restore past versions

### Advanced
- Slash commands in editor: `/image`, `/code`, `/table`, `/task-list`, `/callout` for quick formatting
- Page templates: HR team can create wiki page templates (e.g. "Meeting Notes", "Decision Record", "Incident Report") — applied when creating a new page
- Mentions: @mention a team member in a page — they receive a notification with a link to the mention
- Table of contents: auto-generated from H2/H3 headings — sticky in sidebar on long pages
- Export: download a wiki page as PDF or Markdown

### AI-Powered
- Page summarisation: AI generates a one-paragraph summary of any wiki page — surfaced as a "TL;DR" collapsible at the top of long pages
- Stale page detection: pages not edited in more than 90 days flagged with a "may be outdated" banner — page owner notified to review

## Data Model

```erDiagram
    wiki_pages {
        ulid id PK
        ulid company_id FK
        ulid project_id FK
        ulid parent_page_id FK
        string title
        string slug
        longtext body
        integer sort_order
        ulid created_by FK
        ulid last_edited_by FK
        timestamp last_edited_at
        timestamps created_at/updated_at
    }

    wiki_page_revisions {
        ulid id PK
        ulid page_id FK
        longtext body
        ulid edited_by FK
        timestamp created_at
    }
```

| Column | Notes |
|---|---|
| `parent_page_id` | Self-referential FK for page hierarchy |
| `slug` | URL-friendly title for internal linking |
| `wiki_page_revisions` | Full history — one row per save |

## Permissions

- `projects.wikis.view`
- `projects.wikis.create`
- `projects.wikis.edit`
- `projects.wikis.delete`
- `projects.wikis.manage-templates`

## Filament

- **Resource:** `WikiPageResource`
- **Pages:** `ListWikiPages` (with sidebar tree), `CreateWikiPage`, `EditWikiPage`, `ViewWikiPage` (with TOC and history tab)
- **Custom pages:** None
- **Widgets:** `RecentlyEditedPagesWidget` — five most recently updated wiki pages on project dashboard
- **Nav group:** Resources (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Confluence | Project wiki and knowledge base |
| Notion | Project documentation and notes |
| Slab | Team knowledge base |
| Guru | Internal wiki and knowledge management |

## Related

- [[tasks]]
- [[documents]]
- [[portfolios]]
