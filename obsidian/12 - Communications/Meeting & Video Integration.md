---
tags: [flowflex, domain/communications, meetings, video, phase/5]
domain: Communications
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-07
---

# Meeting & Video Integration

Schedule and join video meetings without leaving FlowFlex. Meeting notes auto-created on start, action items tracked to completion, and recordings stored against the relevant project or deal.

**Who uses it:** All employees, external attendees (CRM contacts)
**Filament Panel:** `communications`
**Depends on:** [[CRM — Contact & Company Management]], [[Task Management]], Google Meet / Zoom / Teams APIs
**Phase:** 5
**Build complexity:** Medium — 3 resources, 1 page, 4 tables

---

## Features

- **Meeting scheduling** — create meetings with title, platform (Google Meet/Zoom/Teams/in-person), start/end time, and invite internal tenants and external CRM contacts
- **Meeting URL auto-generation** — if platform is Google Meet or Zoom, generate meeting URL via API on creation; stored in `meeting_url`
- **Calendar display** — meetings shown on a calendar view in the communications panel; filter by own meetings or team meetings
- **Internal and external attendees** — `meeting_attendees` table supports both `tenant_id` (internal) and `crm_contact_id` (external contact); attendees receive invite email
- **RSVP tracking** — attendees can accept/decline from the invite email; status tracked per attendee
- **Recurring meetings** — type = `recurring` with configurable cadence (daily/weekly/fortnightly/monthly)
- **Auto-created meeting notes** — when a meeting starts, a structured `meeting_notes` record is created automatically; note editor opens for the host
- **Action item capture** — during or after a meeting, create action items with owner, description, and due date; displayed in the assignee's task widget
- **`MeetingCompleted` event** — fires when meeting is marked complete; auto-creates follow-up tasks from unresolved action items
- **Recording link storage** — paste Zoom/Teams recording URL against the meeting record; displayed in the meeting detail view
- **Meeting summary email** — after completion, send an email to all attendees with notes and action items attached
- **Link to project or deal** — associate a meeting with a project, deal, or ticket for context; shown in the related record's timeline

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `meetings`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `description` | text nullable | |
| `type` | enum | `internal`, `external`, `recurring` |
| `platform` | enum | `google_meet`, `zoom`, `teams`, `in_person` |
| `tenant_id` | ulid FK | organiser → tenants |
| `start_at` | timestamp | |
| `end_at` | timestamp | |
| `meeting_url` | string nullable | |
| `recording_url` | string nullable | |
| `status` | enum | `scheduled`, `in_progress`, `completed`, `cancelled` |
| `recurrence` | enum nullable | `daily`, `weekly`, `fortnightly`, `monthly` |
| `project_id` | ulid FK nullable | → projects |
| `deal_id` | ulid FK nullable | → deals |
| `ticket_id` | ulid FK nullable | → tickets |
| `location` | string nullable | room or address for in-person |

### `meeting_attendees`
| Column | Type | Notes |
|---|---|---|
| `meeting_id` | ulid FK | → meetings |
| `tenant_id` | ulid FK nullable | → tenants (internal) |
| `crm_contact_id` | ulid FK nullable | → crm_contacts (external) |
| `status` | enum | `invited`, `accepted`, `declined`, `no_response` |
| `is_host` | boolean default false | |
| `invite_sent_at` | timestamp nullable | |

### `meeting_notes`
| Column | Type | Notes |
|---|---|---|
| `meeting_id` | ulid FK | → meetings |
| `tenant_id` | ulid FK | note author → tenants |
| `body` | text | rich text notes |
| `recorded_at` | timestamp | |
| `is_shared_with_external` | boolean default false | |

### `meeting_action_items`
| Column | Type | Notes |
|---|---|---|
| `meeting_id` | ulid FK | → meetings |
| `tenant_id` | ulid FK | owner → tenants |
| `description` | string | |
| `due_date` | date nullable | |
| `is_completed` | boolean default false | |
| `completed_at` | timestamp nullable | |
| `task_id` | ulid FK nullable | → tasks (if auto-created as task) |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `MeetingScheduled` | `meeting_id`, `attendee_ids` | Invite email to all attendees |
| `MeetingCompleted` | `meeting_id` | Auto-creates follow-up tasks from incomplete action items |

---

## Events Consumed

None — Meetings are triggered by user scheduling actions.

---

## Permissions

```
communications.meetings.view
communications.meetings.create
communications.meetings.edit
communications.meetings.delete
communications.meetings.complete
communications.meeting-notes.view
communications.meeting-notes.create
communications.meeting-notes.edit
communications.meeting-action-items.view
communications.meeting-action-items.create
communications.meeting-action-items.complete
```

---

## Related

- [[Communications Overview]]
- [[Booking & Appointment Scheduling]]
- [[Task Management]]
- [[CRM — Contact & Company Management]]
