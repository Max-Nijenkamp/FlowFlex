---
type: module
domain: Support & Help Desk
domain-key: support
panel: support
module-key: support.chat
status: planned
priority: p2
depends-on: [support.tickets, core.billing, core.rbac, foundation.queues]
soft-depends: [support.canned, crm.contacts]
fires-events: []
consumes-events: []
patterns: [websockets, custom-pages]
tables: [sup_chats, sup_chat_messages, sup_agent_availability]
permission-prefix: support.chat
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Live Chat

Embeddable chat widget for customer websites. Agents respond from an in-panel queue. Chats convert to tickets when offline.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/support/tickets\|support.tickets]] | offline capture + chat→ticket conversion |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, Reverb infra |
| Soft | [[domains/support/canned-responses\|support.canned]] | canned inserts in chat |
| Soft | [[domains/crm/contacts\|crm.contacts]] | visitor email matched to contact |

---

## Core Features

- Embeddable JavaScript widget for customer's website (script tag + company widget key)
- Real-time messaging via Laravel Reverb WebSocket (presence channel per chat — ui-strategy row #8)
- Agent queue: incoming chats distributed to available agents (least-active-chats *(assumed)*)
- Agent availability status: online / away / offline
- Offline mode: when no agents online, capture message as a ticket
- Visitor info: page URL, browser, prior chat history (IP-geo deferred *(assumed: privacy + effort)*)
- Canned responses available in chat
- Chat transcript saved and linkable to a ticket or contact
- Typing indicators, read receipts (whisper events)
- Convert chat to ticket with one click
- Widget channel auth: public visitor token scoped to one chat — never company-wide channel access

---

## Data Model

### sup_chats

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| visitor_id | uuid | widget-local identity |
| visitor_name / visitor_email | string nullable | |
| contact_id | ulid nullable | CRM match |
| agent_id | ulid nullable FK users | |
| status | string default `active` | active / ended / missed |
| page_url / user_agent | string nullable | |
| started_at / ended_at | timestamp | |
| ticket_id | ulid nullable | conversion link |

### sup_chat_messages — id, chat_id FK, company_id (indexed), sender_type (visitor/agent), body (purified, max 4000), read_at nullable, created_at
### sup_agent_availability — id, company_id, agent_id FK unique, status (online/away/offline), updated_at

---

## DTOs

### StartChatData (public widget) — widget_key (valid company key), visitor_name?, visitor_email? (email), message (required, max:4000), page_url
### ChatMessageData — chat_id, body (required, max:4000) — sender resolved from auth context (agent) or visitor token

## Services & Actions

- `ChatService::start(StartChatData $data): ChatData` — agent online → assign + presence channel; none → ticket capture (`missed` chat + ticket)
- `ChatService::sendMessage(...)` — persists + broadcasts on `chat.{chat_id}` private channel (visitor token or agent auth)
- `ConvertChatToTicketAction::run(string $chatId): TicketData` — transcript as description
- `SetAvailabilityAction::run(string $status): void`
- Widget served as built JS asset; authenticates via widget key + per-chat signed token (rate-limited)

---

## Filament

**Nav group:** Live Chat

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ChatQueuePage` | #8 chat custom page | active chats + conversation pane, Reverb realtime, typing/read whispers |
| `ChatTranscriptResource` | #1 (read-only) | archive, ticket/contact links |
| Availability toggle | panel render hook | header status |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('support.chat.view-any') && BillingService::hasModule('support.chat')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Public/portal guard** (HIGH): State the public widget HTTP endpoints run under an explicit scoped guard (Sanctum stateless / dedicated widget guard) limited to widget-key + per-chat token scope, not the panel session guard.

---

## Permissions

`support.chat.respond` · `support.chat.view-transcripts` · `support.chat.manage-widget`

---

## Search & Realtime

Realtime: Reverb — `chat.{chat_id}` private channel (visitor: signed token auth; agent: session) + `company.{id}.support` for queue updates. The heaviest Reverb consumer in the product.

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Invalid widget key rejected; widget endpoints rate-limited
- [ ] Visitor token grants only its own chat channel (cross-chat auth test)
- [ ] No agents online → missed chat + ticket created
- [ ] Messages broadcast + persisted; bodies purified
- [ ] Convert-to-ticket includes transcript
- [ ] Assignment picks least-active online agent

---

## Build Manifest

```
database/migrations/xxxx_create_sup_chats_table.php
database/migrations/xxxx_create_sup_chat_messages_table.php
database/migrations/xxxx_create_sup_agent_availability_table.php
app/Models/Support/{Chat,ChatMessage,AgentAvailability}.php
app/Data/Support/{StartChatData,ChatMessageData,ChatData}.php
app/Services/Support/ChatService.php
app/Actions/Support/{ConvertChatToTicketAction,SetAvailabilityAction}.php
app/Events/Support/{ChatMessageSent,ChatStarted}.php (ShouldBroadcast)
routes/channels.php (chat channel auth)
app/Http/Controllers/ChatWidgetController.php + resources/js/chat-widget/ (built embed)
app/Filament/Support/Pages/ChatQueuePage.php
app/Filament/Support/Resources/ChatTranscriptResource.php
database/factories/Support/{ChatFactory,ChatMessageFactory}.php
tests/Feature/Support/{LiveChatTest,ChatChannelAuthTest}.php
```

---

## Related

- [[domains/support/tickets]]
- [[architecture/websockets]]
- [[architecture/security]]
