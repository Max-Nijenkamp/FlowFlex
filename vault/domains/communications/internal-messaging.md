---
type: module
domain: Communications
panel: comms
module-key: comms.internal
status: planned
color: "#4ADE80"
---

# Internal Messaging

Team chat for internal communication — direct messages and group channels between company users. Slack-lite, inside FlowFlex.

## Core Features

- Direct messages between users
- Group channels: public (anyone joins) or private (invite-only)
- Channel record: name, description, type, members
- Real-time messaging via Reverb WebSocket
- @mentions with notification
- File attachments via Media Library
- Message reactions (emoji)
- Threaded replies on a message
- Unread indicators per channel
- Search messages (Meilisearch)

## Data Model

| Table | Key Columns |
|---|---|
| `comms_channels_internal` | company_id, name, description, type (dm/public/private), created_by |
| `comms_channel_members` | channel_id, company_id, user_id, last_read_at |
| `comms_internal_messages` | company_id, channel_id, user_id, body, parent_message_id |

## Filament

**Nav group:** Messaging

- `InternalMessagingPage` (custom page) — channel sidebar + message thread, real-time via Reverb
- Channel management actions inline

## Cross-Domain / Infra

- Real-time via [[architecture/websockets]]

## Related

- [[domains/communications/shared-inbox]]
- [[architecture/websockets]]
