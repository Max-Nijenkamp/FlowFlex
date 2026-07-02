---
domain: communications
module: shared-inbox
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Shared Inbox — Architecture

## Services & Actions

Interface→Service: `InboxServiceInterface` → `InboxService`. Channels are pluggable via `ChannelDriverRegistry`; the inbox never talks to a provider directly — it routes through the resolved `ChannelDriver`.

| Class | Signature | Responsibility |
|---|---|---|
| `InboxService::handleInbound` | `handleInbound(InboundMessageData): MessageData` | Find-or-create conversation by `(channel, external_party)` open thread; dedupe by `external_id`; auto-link CRM contact; reopen snoozed/resolved threads; broadcast `MessageReceived`; trigger automations (soft). |
| `InboxService::send` | `send(SendMessageData): MessageData` | Route through the channel's `ChannelDriver::send`; capability-validate (e.g. WhatsApp 24h window); record delivery status async. Internal notes are persisted **but never** dispatched to a driver. |
| `InboxService::assign` | `assign(conversationId, userId): void` | Set `assignee_id`. |
| `InboxService::setStatus` | `setStatus(conversationId, status): void` | Transition `open` / `pending` / `resolved` / `snoozed`. |
| `InboxService::snooze` | `snooze(conversationId, until): void` | Set `snoozed_until`, status → `snoozed`; auto-reopened by `ReopenSnoozedCommand` or an inbound message. |
| `ChannelDriverRegistry::register` | `register(string $type, class-string $driver): void` | Channel modules call this in their service providers to plug a driver into the inbox. |

**Internal-note rule:** an internal note (`is_internal_note = true`) is stored on the conversation for the team but is never routed through a `ChannelDriver::send` — enforced in `send`.

## Events

**No cross-domain domain-events are fired or consumed** (per source `fires-events: []`, `consumes-events: []`). See [[../../../architecture/event-bus]] for the platform contract; this module defines none.

| Event | Kind | Notes |
|---|---|---|
| `MessageReceived` | Internal `ShouldBroadcast` (Reverb websocket) | Broadcast on `company.{id}.comms` when a message arrives — drives live inbox arrivals + collision whispers. This is a websocket/broadcast event, **not** a cross-domain domain-event on the event bus. |

## Filament Artifacts

**Nav group:** Inbox

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SharedInboxPage` | #8 inbox custom page | Three-panel; Reverb broadcast for arrivals + collision whispers; per-channel composer driven by driver capabilities. |
| `ChannelResource` | #1 CRUD resource | Channel list / activation; channel-specific config lives in the channel modules' own tables. |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

Every artifact gates on permission **and** module entitlement per [[../../../architecture/filament-patterns]] #1 — the custom page states it explicitly.

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.inbox.view-any')
        && BillingService::hasModule('comms.inbox');
}
```

## Jobs & Scheduling

| Job / Command | Queue | Schedule / Trigger | Idempotency |
|---|---|---|---|
| `ProcessInboundMessageJob` | default | dispatched per inbound webhook | `external_id` dedupe (unique `(conversation_id, external_id)`) |
| `ReopenSnoozedCommand` | default | every 15 min | `snoozed_until <= now` guard |

See [[../../../architecture/queue-jobs]].

## Search & Realtime

- **Meilisearch:** conversation `subject`, `external_party`, and message bodies — indexed as a rolling window *(assumed: latest 1k msgs per conversation aggregate doc)*. See [[../../../architecture/search]].
- **Reverb:** channel `company.{id}.comms` carries two signals — new-message arrivals (`MessageReceived`) and **collision whispers** (client-to-client whisper when another agent is composing a reply to the same conversation). ui-strategy row #8. See [[../../../architecture/websockets]].

## Implementation Notes (tense-softened)

- Threading is designed around the open conversation for a `(channel, external_party)` pair: inbound lands on the existing open thread or opens a new one.
- Inbound processing is designed to be idempotent: webhook retries dedupe on `external_id`, so a re-delivered provider message yields exactly one stored message.
- Send is designed to resolve the conversation's channel driver, capability-validate, dispatch, then record delivery status asynchronously.
