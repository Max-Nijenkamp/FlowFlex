---
domain: communications
module: email-channel
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Email Channel

Connect a company email inbox (support@, info@) so inbound emails become shared-inbox conversations and replies send from that address.

> This is a **shared team address** channel (support@, info@) that plugs into the shared inbox. It is **distinct from `crm.email`** — that module is per-rep **personal mailbox sync** (Gmail/Outlook per user). See [[../../crm/email-integration/_module|CRM Email Integration]].

## Module-key

`comms.email`

**Priority:** p2  
**Panel:** comms  
**Permission prefix:** `comms.email`  
**Tables:** `comms_email_channels`  
**Encrypted fields:** `comms_email_channels.oauth_token`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../shared-inbox/_module\|comms.inbox]] | registers the `email` `ChannelDriver`; owns the message tables |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | inbound parsing jobs |

## Core Features

- **Connect inbox** — v1 = email forwarding to a unique FlowFlex inbound address (`{token}@inbound.flowflex.io` *(assumed)*); OAuth (Gmail/Outlook) deferred to v1.x.
- **Inbound** — incoming emails parsed into conversations in the shared inbox.
- **Outbound** — replies sent from the connected address (via Resend with custom from + reply-to threading *(assumed)*), threaded correctly.
- **Email signature** — per channel, purified HTML.
- **Attachment handling** — inbound attachments stored (core.files), outbound attachments sent.
- **Threading** — match replies to existing conversations via `References` / `In-Reply-To` headers + subject fallback.
- **HTML + plain-text** — both supported; HTML purified before storage.
- **Spam filtering** — basic: provider spam-score header threshold *(assumed)*.

## See features/

- [[features/inbound-parsing|Inbound Parsing]] — signature-verified webhook → resolve channel by token → spam drop → parse → inbox job.
- [[features/outbound-threading|Outbound Threading]] — send from channel address with signature + threading headers so replies thread correctly.

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

## Test Checklist

- [ ] Tenant isolation: inbound resolves company from `inbound_token`; a token never lands mail in another company's inbox.
- [ ] Module gating: `EmailChannelResource` hidden when `comms.email` inactive.
- [ ] Inbound resolves channel by token; unknown token dropped.
- [ ] Reply threads via `References` header; subject fallback works.
- [ ] Outbound sent from channel address with signature.
- [ ] HTML purified; attachments stored tenant-scoped.
- [ ] Spam-flagged mail dropped + logged.
- [ ] Webhook signature-verified.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Writes | `InboxService` / driver | [[../shared-inbox/_module\|comms.inbox]] | message + conversation rows are written by the inbox service; this module only writes `comms_email_channels` |
| Reads | inbound provider webhook | provider (Resend / inbound relay *(assumed)*) | signature-verified payload → normalised `InboundMessageData` |

No cross-domain domain events fired or consumed (per source).

**Data ownership:** `comms.email` writes **only** `comms_email_channels`. Inbound message rows land in `comms_messages` via the shared inbox's `InboxService` / channel driver, never written by this module directly ([[../../../security/data-ownership]]).

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../shared-inbox/_module|Shared Inbox]] · [[../../../architecture/email]]
- [[../../crm/email-integration/_module|CRM Email Integration]] — personal mailboxes, separate module
- [[../../../architecture/patterns/encryption]] · [[../../../glossary]]
