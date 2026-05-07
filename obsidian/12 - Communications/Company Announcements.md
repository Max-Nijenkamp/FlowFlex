---
tags: [flowflex, domain/communications, announcements, phase/5]
domain: Communications
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-07
---

# Company Announcements

Internal broadcast tool. Send targeted announcements to all staff or specific groups, require acknowledgement, and see exactly who has read it.

**Who uses it:** Leadership, HR, managers
**Filament Panel:** `communications`
**Depends on:** Core, [[Notifications & Alerts]]
**Phase:** 5
**Build complexity:** Low — 2 resources, 1 page, 2 tables

---

## Features

- **Rich text announcements** — author announcements with full rich text (bold, bullet points, links, images); save as draft before publishing
- **Audience targeting** — send to all employees, specific departments, specific roles, or manually selected tenants
- **Announcement types** — company-wide, team-specific, or urgent; urgent announcements get banner treatment in the UI
- **Scheduled publishing** — set a future `published_at` date; system publishes automatically
- **Expiry date** — set `expires_at`; expired announcements disappear from the active feed but remain in archive
- **Acknowledgement required** — flag an announcement as requiring acknowledgement; employees must click "I've read this" before the banner dismisses
- **Read receipt tracking** — see who has read and who has acknowledged; percentage bar in the announcements Filament resource
- **Unread reminder** — automatically remind tenants who haven't read a required announcement after configurable days
- **Push notification on publish** — `AnnouncementPublished` event fires; sends in-app notification to all targeted tenants
- **Announcement archive** — searchable history of all past announcements; filterable by type, date, author
- **Pinned announcements** — pin critical announcements to the top of the intranet feed indefinitely
- **Comment support** — optional open comment thread on each announcement; can be disabled per announcement

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `announcements`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `body` | text | rich text HTML |
| `type` | enum | `company`, `team`, `urgent` |
| `tenant_id` | ulid FK | author → tenants |
| `status` | enum | `draft`, `published`, `expired` |
| `published_at` | timestamp nullable | |
| `expires_at` | timestamp nullable | |
| `requires_acknowledgement` | boolean default false | |
| `audience_type` | enum | `all`, `departments`, `roles`, `selected` |
| `audience_ids` | json nullable | department/role/tenant IDs |
| `is_pinned` | boolean default false | |
| `allow_comments` | boolean default true | |
| `read_count` | integer default 0 | denormalised |
| `acknowledged_count` | integer default 0 | denormalised |
| `target_count` | integer default 0 | total targeted tenants |

### `announcement_reads`
| Column | Type | Notes |
|---|---|---|
| `announcement_id` | ulid FK | → announcements |
| `tenant_id` | ulid FK | → tenants |
| `read_at` | timestamp | |
| `acknowledged_at` | timestamp nullable | null if acknowledgement not required |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `AnnouncementPublished` | `announcement_id`, `audience_tenant_ids` | In-app push notification to all targeted tenants |

---

## Events Consumed

None — Announcements are author-triggered.

---

## Permissions

```
communications.announcements.view
communications.announcements.create
communications.announcements.edit
communications.announcements.delete
communications.announcements.publish
communications.announcements.view-reads
```

---

## Related

- [[Communications Overview]]
- [[Company Intranet]]
- [[Notifications & Alerts]]
