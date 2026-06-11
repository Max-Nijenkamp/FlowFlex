---
type: module
domain: Communications
domain-key: communications
panel: comms
module-key: comms.inbox
status: planned
priority: p2
depends-on: [core.billing, core.rbac, core.files, foundation.queues]
soft-depends: [crm.contacts, comms.email, comms.whatsapp, comms.sms, comms.automations]
fires-events: []
consumes-events: []
patterns: [custom-pages, websockets, search]
tables: [comms_channels, comms_conversations, comms_messages]
permission-prefix: comms.inbox
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Shared Inbox

Unified team inbox aggregating email, WhatsApp, SMS (Instagram/Facebook later *(assumed: post-P2)*) messages into one conversation view. Team members collaborate on replies. The hub of the Communications domain — build first in `/comms`; channel modules plug into it.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, attachments, inbound jobs |
| Soft | [[domains/crm/contacts\|crm.contacts]] | auto-link contact by email/phone (find-or-create) |
| Soft | comms.email / comms.whatsapp / comms.sms | the actual channels — inbox is empty without at least one |
| Soft | [[domains/communications/automations\|comms.automations]] | routing/auto-reply on inbound |

---

## Core Features

- Unified conversation list across all connected channels
- Conversation = thread of messages with one contact across one channel
- Assignment: assign conversation to a team member
- Status: open / pending / resolved / snoozed
- Internal notes on conversations (not sent to customer)
- Collision detection: warn when another agent is replying to the same conversation (Reverb whisper)
- Channel badges: visual indicator of source channel per conversation
- Contact linking: auto-link to CRM contact by email/phone
- Conversation tags via spatie/laravel-tags
- Search across all conversations (Meilisearch)
- Snooze a conversation until a later time (auto-reopen)
- **Channel driver contract**: each channel module registers a `ChannelDriver` (send, normalise-inbound, capabilities) — inbox stays channel-agnostic

---

## Data Model

### comms_channels

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| type | string | email / whatsapp / sms (extensible) |
| name | string | display |
| config | jsonb | non-secret channel meta (secrets live in channel-module tables) |
| is_active | boolean | |
| deleted_at | timestamp nullable | |

### comms_conversations

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), channel_id FK | ulid | |
| contact_id | ulid nullable | CRM link |
| external_party | string | email/phone of counterpart |
| subject | string nullable | email only |
| status | string default `open` | open / pending / resolved / snoozed |
| assignee_id | ulid nullable FK users | |
| last_message_at | timestamp | list sort |
| snoozed_until | timestamp nullable | |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, status, last_message_at)`, `(company_id, assignee_id, status)`, `(company_id, channel_id, external_party)` (threading)

### comms_messages

| Column | Type | Notes |
|---|---|---|
| id, conversation_id FK, company_id (indexed) | ulid | |
| direction | string | inbound / outbound |
| channel_type | string | denormalised |
| body | text | purified |
| is_internal_note | boolean default false | |
| sender | string | address/agent name |
| sent_by | ulid nullable FK users | outbound agent |
| external_id | string nullable | provider id, unique `(conversation_id, external_id)` dedupe |
| delivery_status | string nullable | sent/delivered/read/failed |
| sent_at | timestamp | |

GDPR: conversations of erased contacts unlinked, bodies retained as company records per [[architecture/data-lifecycle]] *(assumed)*.

---

## DTOs

### SendMessageData — conversation_id, body (required; channel capability-validated, e.g. WhatsApp 24h-window check via driver), is_internal_note, attachments[]
### InboundMessageData (normalised driver output) — channel_id, external_party, body, external_id, attachments, meta

## Services & Actions

Interface→Service: `InboxServiceInterface` → `InboxService`.

- `handleInbound(InboundMessageData $data): MessageData` — find-or-create conversation by `(channel, external_party)` open thread; dedupe by external_id; contact auto-link; reopens snoozed/resolved; broadcasts; triggers automations (soft)
- `send(SendMessageData $data): MessageData` — routes through the channel's `ChannelDriver::send`; records delivery status async
- `assign` / `setStatus` / `snooze(until)`
- `ChannelDriverRegistry::register(string $type, class-string $driver)` — channel modules call in their providers
- `ReopenSnoozedCommand` — scheduled

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ProcessInboundMessageJob` | default | per webhook | external_id dedupe |
| `ReopenSnoozedCommand` | default | every 15 min | `snoozed_until <= now` guard |

---

## Filament

**Nav group:** Inbox

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SharedInboxPage` | #8 inbox custom page | three-panel; Reverb broadcast for arrivals + collision whispers; per-channel composer (driver capabilities) |
| `ChannelResource` | #1 CRUD resource | channel list/activation (config in channel modules) |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('comms.inbox.view-any') && BillingService::hasModule('comms.inbox')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a rate limiter (e.g. throttle middleware on the channel webhook controllers) to protect the inbound pipeline from flooding.
- **Upload contract** (medium): Specify allowed MIME/extension whitelist, max upload size, and tenant-scoped path companies/{company_id}/comms/... for message attachments.

---

## Permissions

`comms.inbox.view-any` · `comms.inbox.reply` · `comms.inbox.assign` · `comms.inbox.manage-channels`

---

## Search & Realtime

Meilisearch: conversation subject, external_party, message bodies (rolling window *(assumed: latest 1k msgs per conversation aggregate doc)*). Realtime: Reverb on `company.{id}.comms` — new message + collision whispers (ui-strategy row #8).

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Inbound threads onto existing open conversation per (channel, party); new otherwise
- [ ] external_id dedupe (webhook retry = one message)
- [ ] Internal note never sent through driver
- [ ] Send routes through correct driver; inactive channel rejected
- [ ] Snooze hides + auto-reopens; inbound reopens immediately
- [ ] Contact auto-link by email/phone when CRM active
- [ ] Broadcast event on arrival; bodies purified

---

## Build Manifest

```
database/migrations/xxxx_create_comms_channels_table.php
database/migrations/xxxx_create_comms_conversations_table.php
database/migrations/xxxx_create_comms_messages_table.php
app/Models/Comms/{Channel,Conversation,Message}.php
app/Data/Comms/{SendMessageData,InboundMessageData,MessageData,ConversationData}.php
app/Contracts/Comms/{InboxServiceInterface,ChannelDriverInterface}.php
app/Services/Comms/InboxService.php
app/Support/Comms/ChannelDriverRegistry.php
app/Providers/Comms/CommsServiceProvider.php
app/Jobs/Comms/ProcessInboundMessageJob.php
app/Console/Commands/Comms/ReopenSnoozedCommand.php
app/Events/Comms/MessageReceived.php (ShouldBroadcast)
app/Filament/Comms/Pages/SharedInboxPage.php
app/Filament/Comms/Resources/ChannelResource.php
database/factories/Comms/{ChannelFactory,ConversationFactory,MessageFactory}.php
tests/Feature/Comms/{InboxThreadingTest,InboxSendTest}.php
```

---

## Related

- [[domains/communications/whatsapp]]
- [[domains/communications/email-channel]]
- [[domains/communications/sms-channel]]
- [[domains/crm/contacts]]
- [[architecture/websockets]]
