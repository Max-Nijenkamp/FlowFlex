---
domain: communications
module: sms-channel
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# SMS Channel

Send and receive SMS via Twilio or Vonage. Inbound SMS lands in the shared inbox; outbound goes from a company virtual number. STOP opt-outs are honoured everywhere.

> Plugs into the shared inbox as a `ChannelDriver`. Owns only its config + opt-out tables; message rows live in `comms_messages` (inbox-owned).

## Module-key

`comms.sms`

**Priority:** p2  
**Panel:** comms  
**Permission prefix:** `comms.sms`  
**Tables:** `comms_sms_config`, `comms_sms_optouts`  
**Encrypted fields:** `comms_sms_config.api_key`, `comms_sms_config.api_secret`, `comms_sms_config.webhook_secret`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../shared-inbox/_module\|comms.inbox]] | registers the `sms` `ChannelDriver`; owns message tables |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | webhook + status-callback jobs |
| Soft | [[../broadcast/_module\|comms.broadcast]] | SMS broadcasts (opt-outs enforced) |

## Core Features

- Connect an SMS provider (Twilio / Vonage — driver abstraction *(assumed: Twilio first)*): virtual number + API credentials.
- Inbound SMS → shared inbox conversation, threaded by E.164 number.
- Outbound SMS from the company number.
- Phone numbers E.164 via `propaganistas/laravel-phone`.
- Delivery status tracking (sent / delivered / failed) via provider callbacks.
- Character count + segment estimation (160 GSM / 70 unicode chars per segment *(assumed)*).
- **Opt-out handling** (STOP keyword): auto-mark the number unsubscribed; sends to opted-out numbers blocked everywhere (inbox + broadcast).
- Cost tracking per message (provider rate, cents).

## See features/

- [[features/inbound-optout|Inbound & Opt-out]] — STOP → opt-out row; normal inbound → inbox; compliance blocking.
- [[features/outbound-send|Outbound Send]] — send from company number; segment estimate; opted-out block.
- [[features/cost-tracking|Cost Tracking]] — per-message cost from provider callbacks.

## Build Manifest

```
database/migrations/xxxx_create_comms_sms_config_table.php
database/migrations/xxxx_create_comms_sms_optouts_table.php
app/Models/Comms/{SmsConfig,SmsOptout}.php
app/Data/Comms/ConnectSmsData.php
app/Support/Comms/Drivers/SmsDriver.php
app/Services/Comms/OptOutService.php
app/Exceptions/Comms/RecipientOptedOutException.php
app/Http/Controllers/Webhooks/SmsWebhookController.php
app/Filament/Comms/Resources/SmsChannelResource.php
database/factories/Comms/SmsConfigFactory.php
tests/Feature/Comms/{SmsDriverTest,SmsOptOutTest}.php
```

## Test Checklist

- [ ] Tenant isolation: webhook resolves company from `webhook_secret` / number; config + opt-outs never cross companies.
- [ ] Module gating: SMS channel resource + opt-out list hidden when `comms.sms` inactive.
- [ ] Credentials ciphertext; webhook signature verified.
- [ ] STOP inbound creates opt-out; subsequent sends blocked (inbox + broadcast).
- [ ] Segment estimation (GSM vs unicode fixtures).
- [ ] Delivery callbacks update status; cost recorded.
- [ ] Inbound threads by E.164.
- [ ] Provider API mocked (`Http::fake`).

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Registers | `ChannelDriver` (`sms`) | [[../shared-inbox/_module\|comms.inbox]] | driver contract; inbox owns `comms_messages` |
| Writes (via inbox) | `InboundMessageData` → `InboxService` | [[../shared-inbox/_module\|comms.inbox]] | inbox writes the message row — sms does not |
| Reads | provider SMS API | provider (Twilio / Vonage) | send, delivery callbacks |
| Provides | `OptOutService::isOptedOut` | [[../broadcast/_module\|comms.broadcast]] | broadcast materialisation excludes opted-out numbers |

No cross-domain **domain events** fired or consumed (see [[../../../architecture/event-bus]]).

**Data ownership:** `comms.sms` writes **only** `comms_sms_config` and `comms_sms_optouts`. Message rows land in `comms_messages` via the inbox's `InboxService` / channel driver, never written by this module directly ([[../../../security/data-ownership]]).

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../shared-inbox/_module|Shared Inbox]] · [[../broadcast/_module|Broadcast]] · [[../../../architecture/patterns/encryption]]
