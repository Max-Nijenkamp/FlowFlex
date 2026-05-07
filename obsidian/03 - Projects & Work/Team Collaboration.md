---
tags: [flowflex, domain/projects, collaboration, comments, phase/8]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: planned
last_updated: 2026-05-07
---

# Team Collaboration

Context-aware discussion attached to the work itself. Comments, @mentions, reactions, and activity feeds — all visible next to the record they relate to. Reduces Slack noise, keeps decisions close to where they were made.

**Who uses it:** All employees
**Filament Panel:** `projects`
**Depends on:** [[Task Management]], [[Notifications & Alerts]]
**Phase:** 8
**Build complexity:** Medium — 2 resources, 3 tables

---

## Features

- **Comment threads on any record** — tasks, projects, documents, field jobs, deals; any module can adopt the Commentable trait
- **@mention users** — triggers in-app notification and email via [[Notifications & Alerts]]
- **@mention teams** — notifies all active members of the team
- **File attachments in comments** — uploaded to S3 via [[File Storage]]
- **Emoji reactions to comments** — per-user reaction, shows aggregate counts
- **Threaded replies** — reply to a specific comment, shown indented
- **Edit and delete own comments** — edit history preserved in `comment_edits`
- **Activity feed per project** — chronological log of all actions, comments, and status changes
- **Project announcements** — pinned update at top of project visible to all members
- **Watching / following** — subscribe to updates on any record without being assigned; unsubscribe anytime
- **Read receipts on comments** — show who has seen a comment (optional per workspace setting)
- **Mute threads** — opt out of notifications for a specific thread

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `comments`
| Column | Type | Notes |
|---|---|---|
| `commentable_type` | string | morph type (Task, Project, Document, etc.) |
| `commentable_id` | ulid | morph id |
| `tenant_id` | ulid FK | author |
| `parent_id` | ulid FK nullable | → comments; for threaded replies |
| `body` | text | raw markdown |
| `body_html` | text | rendered HTML (cached on save) |
| `is_edited` | boolean | default false |
| `is_pinned` | boolean | project announcement if true |

### `comment_reactions`
| Column | Type | Notes |
|---|---|---|
| `comment_id` | ulid FK | → comments |
| `tenant_id` | ulid FK | who reacted |
| `emoji` | string(10) | emoji character or shortcode |

### `comment_edits`
| Column | Type | Notes |
|---|---|---|
| `comment_id` | ulid FK | → comments |
| `previous_body` | text | body before edit |
| `edited_at` | timestamp | |

### `comment_watchers`
| Column | Type | Notes |
|---|---|---|
| `watchable_type` | string | morph type |
| `watchable_id` | ulid | morph id |
| `tenant_id` | ulid FK | watcher |
| `is_muted` | boolean | default false |

### `record_activities`
| Column | Type | Notes |
|---|---|---|
| `activitable_type` | string | morph type (Project, Task, etc.) |
| `activitable_id` | ulid | morph id |
| `tenant_id` | ulid FK nullable | who triggered it |
| `type` | enum | `comment`, `status_change`, `assignment`, `field_change`, `attachment` |
| `description` | string | human-readable log line |
| `meta` | json nullable | before/after values for field changes |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `CommentPosted` | `comment_id`, `commentable_type`, `commentable_id`, `mentioned_tenant_ids` | [[Notifications & Alerts]] (notify mentioned users + watchers) |
| `RecordWatched` | `watchable_type`, `watchable_id`, `tenant_id` | — |

---

## Permissions

```
projects.comments.view
projects.comments.create
projects.comments.edit-own
projects.comments.delete-own
projects.comments.delete-any
projects.activities.view
```

---

## Related

- [[Projects Overview]]
- [[Task Management]]
- [[Notifications & Alerts]]
- [[Internal Messaging & Chat]]
- [[Document Management]]
