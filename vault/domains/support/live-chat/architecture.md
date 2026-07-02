---
domain: support
module: live-chat
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Live Chat — Architecture

## Services & Actions

- `ChatService::start(StartChatData $data): ChatData` — agent online → assign (least-active *(assumed)*) + presence channel; none online → ticket capture (`missed` chat + ticket via `TicketService`)
- `ChatService::sendMessage(ChatMessageData $data)` — persists + broadcasts on the `chat.{chat_id}` private channel (visitor token or agent auth)
- `ConvertChatToTicketAction::run(string $chatId): TicketData` — transcript as ticket description (via `TicketService`)
- `SetAvailabilityAction::run(string $status): void` — online/away/offline
- Widget served as a built JS asset; authenticates via widget key + per-chat signed token (rate-limited)

---

## Events (broadcast)

| Event | Channel | Notes |
|---|---|---|
| `ChatStarted` | `company.{id}.support` | queue update |
| `ChatMessageSent` | `chat.{chat_id}` (private) | visitor: signed token auth; agent: session |

Both `ShouldBroadcast`. Typing/read receipts are whisper events. Channel auth in `routes/channels.php`.

---

## Filament Artifacts

**Nav group:** Live Chat

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ChatQueuePage` | #8 chat custom page | active chats + conversation pane, Reverb realtime, typing/read whispers |
| `ChatTranscriptResource` | #1 (read-only) | archive, ticket/contact links |
| Availability toggle | panel render hook | header status |

**Access contract:** panel artifacts gate on `canAccess() = Auth::user()->can('support.chat.view-any') && BillingService::hasModule('support.chat')` per [[../../../architecture/filament-patterns]] #1 — `ChatQueuePage` states it explicitly. Public widget endpoints run under a scoped widget guard (see [[./security]]).

---

## Search & Realtime

Realtime: Reverb — `chat.{chat_id}` private channel (visitor signed token / agent session) + `company.{id}.support` for the queue. Heaviest Reverb consumer in the product. No Meilisearch index in v1.

See [[./security]] for the widget guard + per-chat token scoping (HIGH).
