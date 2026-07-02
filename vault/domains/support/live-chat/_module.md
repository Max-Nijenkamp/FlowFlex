---
domain: support
module: live-chat
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Live Chat

Embeddable chat widget for customer websites. Agents respond from an in-panel queue. Chats convert to tickets when offline. The heaviest Reverb consumer in the product.

---

## Module-key

`support.chat`

**Priority:** p2  
**Panel:** support  
**Permission prefix:** `support.chat`  
**Tables:** `sup_chats`, `sup_chat_messages`, `sup_agent_availability`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tickets/_module\|support.tickets]] | offline capture + chat→ticket conversion |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../foundation/queue-workers/_module\|foundation.queues]] | gating, permissions, Reverb infra |
| Soft | [[../canned-responses/_module\|support.canned]] | canned inserts in chat |
| Soft | [[../../crm/contacts/_module\|crm.contacts]] | visitor email matched to a contact |

---

## Core Features

- Embeddable JavaScript widget for the customer's website (script tag + company widget key)
- Real-time messaging via Laravel Reverb WebSocket (presence channel per chat — ui-strategy row #8)
- Agent queue: incoming chats distributed to available agents (least-active-chats *(assumed)*)
- Agent availability status: online / away / offline
- Offline mode: when no agents online, capture the message as a ticket
- Visitor info: page URL, browser, prior chat history (IP-geo deferred *(assumed: privacy + effort)*)
- Canned responses available in chat
- Chat transcript saved and linkable to a ticket or contact
- Typing indicators, read receipts (whisper events)
- Convert chat to ticket with one click
- Widget channel auth: public visitor token scoped to one chat — never company-wide channel access

See [[./features/chat-widget|Chat Widget]], [[./features/agent-queue|Agent Queue]], and [[./features/chat-to-ticket|Chat-to-Ticket]] features.

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Invalid widget key rejected; widget endpoints rate-limited
- [ ] Visitor token grants only its own chat channel (cross-chat auth test)
- [ ] No agents online → missed chat + ticket created
- [ ] Messages broadcast + persisted; bodies purified
- [ ] Convert-to-ticket includes transcript
- [ ] Assignment picks least-active online agent

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Feeds | `ConvertChatToTicketAction` / offline capture | support.tickets | creates ticket via `TicketService` (Tickets owns the write) |
| Reads | `ContactService` | crm.contacts (soft) | visitor email → contact match |
| Reads | `RenderCannedResponseAction` | support.canned (soft) | canned inserts in chat |
| Public | widget messaging | unauthenticated visitors | scoped widget-key + per-chat token |

**Data ownership:** `support.chat` writes only `sup_chats`, `sup_chat_messages`, `sup_agent_availability`. Ticket creation on offline/convert goes through `TicketService` — never a direct `sup_tickets` write ([[../../../security/data-ownership]]).

---

## Related

- [[../tickets/_module|support.tickets]]
- [[../../../architecture/websockets]]
- [[../../../architecture/security]]
