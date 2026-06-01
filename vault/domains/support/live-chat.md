---
type: module
domain: Support & Help Desk
panel: support
module-key: support.chat
status: planned
color: "#4ADE80"
---

# Live Chat

Embeddable chat widget for customer websites. Agents respond from an in-panel queue. Chats convert to tickets when offline.

## Core Features

- Embeddable JavaScript widget for customer's website
- Real-time messaging via Laravel Reverb WebSocket
- Agent queue: incoming chats distributed to available agents
- Agent availability status: online / away / offline
- Offline mode: when no agents online, capture message as a ticket
- Visitor info: page URL, browser, location (IP-based), prior chat history
- Canned responses available in chat (see [[domains/support/canned-responses]])
- Chat transcript saved and linkable to a ticket or contact
- Typing indicators, read receipts
- Convert chat to ticket with one click

## Data Model

| Table | Key Columns |
|---|---|
| `sup_chats` | company_id, visitor_id, visitor_name, visitor_email, agent_id, status (active/ended/missed), started_at, ended_at, ticket_id |
| `sup_chat_messages` | company_id, chat_id, sender_type (visitor/agent), body, read_at |
| `sup_agent_availability` | company_id, agent_id, status, updated_at |

## Filament

**Nav group:** Live Chat

- `ChatQueuePage` (custom page) — active chats list + conversation view, real-time via Reverb
- `ChatTranscriptResource` — read-only past chat archive
- Agent availability toggle in panel header

## Cross-Domain / Infra

- Heavy use of [[architecture/websockets]] (Reverb)
- Public widget served separately; authenticates via company widget key

## Related

- [[domains/support/tickets]]
- [[architecture/websockets]]
