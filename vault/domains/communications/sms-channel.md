---
type: module
domain: Communications
panel: comms
module-key: comms.sms
status: planned
color: "#4ADE80"
---

# SMS Channel

Send and receive SMS via Twilio or Vonage. Inbound SMS lands in the shared inbox; outbound from a company virtual number.

## Core Features

- Connect SMS provider (Twilio / Vonage): virtual number, API credentials
- Inbound SMS → shared inbox conversation
- Outbound SMS from the company number
- Phone numbers E.164 via `propaganistas/laravel-phone`
- Delivery status tracking (sent, delivered, failed)
- Character count + segment estimation (160 chars per segment)
- Opt-out handling (STOP keyword) — auto-mark recipient as unsubscribed
- Cost tracking per message (provider rate)

## Data Model

| Table | Key Columns |
|---|---|
| `comms_sms_config` | company_id, channel_id, provider, virtual_number_e164, api_key (encrypted), api_secret (encrypted) |
| `comms_sms_optouts` | company_id, phone_e164, opted_out_at |

Messages flow through `comms_messages` (channel_type = sms).

## Filament

**Nav group:** Settings

- `SmsChannelResource` — connect provider, configure number
- Sending via shared inbox composer

## Cross-Domain / Security

- API credentials encrypted (see [[architecture/patterns/encryption]])
- Inbound via provider webhook with signature verification

## Related

- [[domains/communications/shared-inbox]]
- [[domains/communications/broadcast]]
