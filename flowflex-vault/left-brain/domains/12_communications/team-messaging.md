---
type: module
domain: Communications & Internal Comms
panel: comms
phase: 5
status: planned
cssclasses: domain-comms
migration_range: 550000–550499
last_updated: 2026-05-09
---

# Team Messaging

Real-time internal messaging. Channels, direct messages, threads, and search. Slack-like UX built into FlowFlex so teams don't need a separate tool.

---

## Channels

Organised conversation spaces:
- **Public channels**: visible and joinable by all
- **Private channels**: invite-only
- **Project channels**: auto-created when project is created
- **External channels**: include guests (clients, contractors) without full account

Channel naming: `#team-engineering`, `#client-acme`, `#general`

---

## Messaging Features

- Rich text: bold, italic, code blocks, lists
- File sharing: drag-and-drop images, documents
- Emoji reactions
- @mention: ping a person (notification) or @channel / @here
- Thread replies: keep discussion organised under one message
- Pin important messages to channel
- Bookmarks: save messages for later

---

## Search

Full-text search across all accessible messages:
- Filter by channel, person, date
- Find files shared in messages
- Jump to any result in context

---

## Integrations

In-FlowFlex notifications route to relevant channels:
- New deal closed → #sales-wins
- Invoice overdue → #finance-alerts
- Critical support ticket → #support-urgent
- CI/CD pipeline failed → #engineering-deploys

---

## Status & Presence

- Set status: In a meeting, On holiday, Remote, Focused (DND)
- Availability hours: notifications silenced outside working hours
- Time zone shown per user

---

## Data Model

### `comms_channels`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(100) | |
| type | enum | public/private/project/external |
| project_id | ulid | nullable FK |

### `comms_messages`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| channel_id | ulid | FK |
| thread_parent_id | ulid | nullable self-FK |
| author_id | ulid | FK |
| content | text | |
| sent_at | timestamp | |
| edited_at | timestamp | nullable |

---

## Migration

```
550000_create_comms_channels_table
550001_create_comms_messages_table
550002_create_comms_reactions_table
```

---

## Related

- [[MOC_Communications]]
- [[email-integration]]
- [[knowledge-base-wiki]]
- [[video-meeting-integration]]
