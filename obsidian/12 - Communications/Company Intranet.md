---
tags: [flowflex, domain/communications, intranet, noticeboard, phase/5]
domain: Communications
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-07
---

# Company Intranet

The company homepage. Where employees start their day — company news, quick links, org chart, and curated knowledge pages, all in one place.

**Who uses it:** All employees
**Filament Panel:** `communications`
**Depends on:** [[HR — Employee Profiles]], [[Company Announcements]], Core
**Phase:** 5
**Build complexity:** Medium — 3 resources, 1 page, 3 tables

---

## Features

- **Company news feed** — latest internal news articles, announcements, and updates; sorted by `published_at` descending; pinnable
- **Knowledge pages** — CMS-style internal wiki pages with rich text (JSON block structure); organised by category; full-text searchable
- **Page publishing workflow** — pages saved as draft; published by an admin; version history via `updated_at` audit trail
- **Org chart** — interactive tree view of the company hierarchy; nodes for each tenant showing name, title, department, and profile photo
- **Org chart auto-update** — `EmployeeProfileCreated` event adds a new `org_chart_nodes` record; `EmployeeTerminated` event marks node inactive
- **Quick links** — role-curated shortcut tiles to frequently used resources (external URLs or internal FlowFlex pages)
- **Events calendar widget** — upcoming company events pulled from [[Events & Webinars]]; shown on intranet homepage
- **Team spotlights** — feature an employee of the week via a pinned intranet news item
- **Search** — unified search across pages and news items; results ranked by relevance and recency
- **Customisable homepage layout** — role-based widget configuration; HR sees open positions, Finance sees invoice queue, etc.
- **New employee welcome banner** — when a new employee logs in for the first time, display a personalised welcome banner linking to induction resources
- **Announcements integration** — company announcements from [[Company Announcements]] surfaced in the news feed; urgent announcements shown as banners

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `intranet_pages`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `slug` | string unique per company | |
| `body` | json | block-based content structure |
| `category` | string nullable | e.g. "Policies", "Benefits", "IT" |
| `author_id` | ulid FK | → tenants |
| `published_at` | timestamp nullable | |
| `is_pinned` | boolean default false | |
| `is_published` | boolean default false | |
| `sort_order` | integer default 0 | |
| `view_count` | integer default 0 | |

### `intranet_news`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `body` | text | rich text |
| `image_file_id` | ulid FK nullable | → files |
| `tenant_id` | ulid FK | author → tenants |
| `published_at` | timestamp nullable | |
| `is_pinned` | boolean default false | |
| `is_published` | boolean default false | |

### `org_chart_nodes`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | → tenants |
| `parent_id` | ulid FK nullable | → org_chart_nodes (self-referential) |
| `title` | string | job title |
| `department_id` | ulid FK nullable | → departments |
| `sort_order` | integer default 0 | |
| `is_active` | boolean default true | |

---

## Events Fired

None — Intranet is a content display module.

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `EmployeeProfileCreated` | [[HR — Employee Profiles]] | Creates an `org_chart_nodes` record for the new employee |
| `EmployeeTerminated` | [[HR — Employee Profiles]] | Sets `org_chart_nodes.is_active = false` for the departed employee |

---

## Permissions

```
communications.intranet-pages.view
communications.intranet-pages.create
communications.intranet-pages.edit
communications.intranet-pages.delete
communications.intranet-pages.publish
communications.intranet-news.view
communications.intranet-news.create
communications.intranet-news.edit
communications.intranet-news.delete
communications.intranet-news.publish
communications.org-chart.view
communications.org-chart.edit
```

---

## Related

- [[Communications Overview]]
- [[Company Announcements]]
- [[HR — Employee Profiles]]
- [[Events & Webinars]]
