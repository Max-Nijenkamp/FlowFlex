---
domain: support
module: live-chat
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ChatQueuePage` | #8 chat custom page | [[../../../architecture/patterns/page-blueprints#Inbox / Chat / Conversation]] | active/waiting chats + conversation pane; Reverb realtime, typing/read whispers |
| `ChatTranscriptResource` | #1 CRUD resource | tweaks: read-only-flow-owned (`ChatService` owns writes) | archive, ticket/contact links; `canCreate(): false` |
| Availability toggle | #10 render hook (panel header) | [[../../../architecture/patterns/page-blueprints#Notification Bell (render hook, not a page)]] | online / away / offline; `SetAvailabilityAction` |

**Access contract (mandatory):** every panel artifact gates on
`canAccess() = Auth::user()->can('support.chat.view-any') && BillingService::hasModule('support.chat')`
per [[../../../architecture/filament-patterns]] #1. `ChatQueuePage` is a custom page and MUST state this explicitly — Filament does not auto-gate custom pages. Public widget endpoints run under a **scoped widget guard** (widget-key + per-chat signed token), never the panel session — see [[./security]].

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Chat message append (visitor / agent) | n/a | append-only insert into `sup_chat_messages` + broadcast; no in-place mutation of an existing row |
| Chat claim / assignment | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the chat row — prevents two agents claiming the same waiting chat ([[../../../architecture/patterns/states]]) |
| Convert-to-ticket / offline capture | Pessimistic | lock the chat, guard `ticket_id` is null, stamp once; the ticket is created via `TicketService` (Tickets owns that locked write) |
| Agent availability toggle | Optimistic | `updated_at` stale-check on the agent's own `sup_agent_availability` row ([[../../../architecture/patterns/optimistic-locking]]) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

Realtime: Reverb — `chat.{chat_id}` private channel (visitor signed token / agent session) + `company.{id}.support` for the queue. Heaviest Reverb consumer in the product. No Meilisearch index in v1.

See [[./security]] for the widget guard + per-chat token scoping (HIGH).
