---
type: module
domain: Communications
domain-key: communications
panel: comms
module-key: comms.email
status: planned
priority: p2
depends-on: [comms.inbox, core.billing, core.rbac, foundation.queues]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [encryption, queues]
tables: [comms_email_channels]
permission-prefix: comms.email
encrypted-fields: ["comms_email_channels.oauth_token"]
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Email Channel

Connect a company email inbox (support@, info@) so inbound emails become shared-inbox conversations and replies send from that address.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/communications/shared-inbox\|comms.inbox]] | registers the `email` ChannelDriver |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, inbound parsing jobs |

(Distinct from `crm.email` — that's per-rep personal mailbox sync; this is shared team addresses.)

---

## Core Features

- Connect inbox: **v1 = email forwarding to a unique FlowFlex inbound address** (`{token}@inbound.flowflex.io` *(assumed)*); OAuth (Gmail/Outlook) = v1.x
- Inbound: incoming emails parsed into conversations in the shared inbox
- Outbound: replies sent from the connected address (via Resend with custom from + reply-to threading *(assumed)*), threaded correctly
- Email signature per channel
- Attachment handling: inbound attachments stored (core.files), outbound attachments sent
- Threading: match replies to existing conversations via References/In-Reply-To headers + subject fallback
- HTML and plain-text email support (HTML purified)
- Spam filtering (basic: provider spam score header threshold *(assumed)*)

---

## Data Model

### comms_email_channels

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), channel_id FK | ulid | |
| address | string | public address (support@company.com) |
| inbound_token | string unique | forwarding address token |
| connection_type | string | forward / oauth (v1.x) |
| 🔐 oauth_token | text nullable | encrypted (v1.x) |
| signature | text nullable | purified HTML |

---

## DTOs

### ConnectEmailChannelData — address (required, email), signature?
Inbound payload → normalised `InboundMessageData` (inbox contract).

## Services & Actions

- `EmailChannelDriver implements ChannelDriverInterface` — send via Resend (from = channel address), inject signature, set threading headers
- `InboundEmailWebhookController` — signature-verified provider webhook; resolves channel by inbound_token; spam-scored drop; parse → inbox job
- Threading: References/In-Reply-To message-id map → conversation; fallback `(channel, from-address)` open conversation

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `EmailChannelResource` | #1 CRUD resource | shows forwarding address to configure, signature editor, test-connection action |

---

## Permissions

`comms.email.manage` (+ inbox permissions for messaging)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Inbound resolves channel by token; unknown token dropped
- [ ] Reply threads via References header; subject fallback works
- [ ] Outbound sent from channel address with signature
- [ ] HTML purified; attachments stored tenant-scoped
- [ ] Spam-flagged mail dropped + logged
- [ ] Webhook signature-verified

---

## Build Manifest

```
database/migrations/xxxx_create_comms_email_channels_table.php
app/Models/Comms/EmailChannel.php
app/Data/Comms/ConnectEmailChannelData.php
app/Support/Comms/Drivers/EmailChannelDriver.php
app/Http/Controllers/Webhooks/InboundCommsEmailController.php
app/Filament/Comms/Resources/EmailChannelResource.php
database/factories/Comms/EmailChannelFactory.php
tests/Feature/Comms/{EmailChannelTest,EmailThreadingTest}.php
```

---

## Related

- [[domains/communications/shared-inbox]]
- [[architecture/email]]
- [[domains/crm/email-integration]] — personal mailboxes, separate module
