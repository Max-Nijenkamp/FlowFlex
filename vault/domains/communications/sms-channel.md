---
type: module
domain: Communications
domain-key: communications
panel: comms
module-key: comms.sms
status: planned
priority: p2
depends-on: [comms.inbox, core.billing, core.rbac, foundation.queues]
soft-depends: [comms.broadcast]
fires-events: []
consumes-events: []
patterns: [encryption, money]
tables: [comms_sms_config, comms_sms_optouts]
permission-prefix: comms.sms
encrypted-fields: ["comms_sms_config.api_key", "comms_sms_config.api_secret", "comms_sms_config.webhook_secret"]
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# SMS Channel

Send and receive SMS via Twilio or Vonage. Inbound SMS lands in the shared inbox; outbound from a company virtual number.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/communications/shared-inbox\|comms.inbox]] | registers the `sms` ChannelDriver |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, webhook jobs |
| Soft | [[domains/communications/broadcast\|comms.broadcast]] | SMS broadcasts |

---

## Core Features

- Connect SMS provider (Twilio / Vonage — driver abstraction *(assumed: Twilio first)*): virtual number, API credentials
- Inbound SMS → shared inbox conversation (E.164 threading)
- Outbound SMS from the company number
- Phone numbers E.164 via `propaganistas/laravel-phone`
- Delivery status tracking (sent, delivered, failed) via provider callbacks
- Character count + segment estimation (160 chars/segment, 70 unicode *(assumed)*)
- Opt-out handling (STOP keyword) — auto-mark recipient as unsubscribed; sends to opted-out numbers blocked everywhere (inbox + broadcast)
- Cost tracking per message (provider rate, cents)

---

## Data Model

### comms_sms_config

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) unique, channel_id FK | ulid | one number v1 *(assumed)* |
| provider | string | twilio / vonage |
| virtual_number_e164 | string | |
| 🔐 api_key / 🔐 api_secret | text | encrypted |
| 🔐 webhook_secret | text | encrypted cast — callback verification |

### comms_sms_optouts — id, company_id (indexed), phone_e164 (unique per company), opted_out_at

Messages flow through `comms_messages` (channel_type = sms; `cost_cents` in message meta *(assumed: jsonb meta column on comms_messages)*).

---

## DTOs

### ConnectSmsData — provider (in set), virtual_number_e164 (phone), api_key/api_secret (verified against provider before save)

## Services & Actions

- `SmsDriver implements ChannelDriverInterface` — `send()` throws `RecipientOptedOutException`; segment estimate; records cost
- `SmsWebhookController` — signature-verified; inbound STOP → opt-out row + confirmation *(assumed: provider handles confirmation)*; normal inbound → inbox; status callbacks update delivery_status
- `OptOutService::isOptedOut(string $e164): bool` — checked by driver + broadcast materialisation

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SmsChannelResource` | #1 CRUD resource | connect provider, credentials write-only, test send |
| Opt-out list (relation/page) | #1 read-only | compliance view |

Sending via shared inbox composer (segment counter shown).

---

## Permissions

`comms.sms.manage` (+ inbox permissions)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Credentials ciphertext; webhook signature verified
- [ ] STOP inbound creates opt-out; subsequent sends blocked (inbox + broadcast)
- [ ] Segment estimation (GSM vs unicode fixtures)
- [ ] Delivery callbacks update status; cost recorded
- [ ] Inbound threads by E.164
- [ ] Provider API mocked

---

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

---

## Related

- [[domains/communications/shared-inbox]]
- [[domains/communications/broadcast]]
- [[architecture/patterns/encryption]]
