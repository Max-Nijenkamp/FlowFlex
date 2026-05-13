---
type: module
domain: Omnichannel Inbox
panel: inbox
module-key: inbox.sms
status: planned
color: "#4ADE80"
---

# SMS Channel

> Twilio or Vonage two-way SMS conversations in the shared inbox — MMS support, opt-out (STOP) handling, SMS templates, and TCPA/GDPR consent tracking before outbound sends.

**Panel:** `/inbox`
**Module key:** `inbox.sms`

## What It Does

SMS Channel connects one or more business SMS numbers to the shared inbox via Twilio Messaging API or Vonage SMS API. Inbound SMS and MMS messages from customers arrive as conversations in the shared inbox, and agents reply from the same workspace they use for email and WhatsApp. Two-way conversations are fully supported — the customer's reply continues the same conversation thread. STOP keyword opt-outs are handled automatically in compliance with TCPA regulations: when a contact replies STOP, their number is added to the opt-out list and all future outbound sends to that number are blocked. A consent-tracking layer records explicit opt-in evidence before any outbound message is sent.

## Features

### Core
- Connect Twilio or Vonage phone numbers. Credentials stored encrypted per channel. Webhook receiver handles inbound messages and delivery status updates.
- Inbound SMS: creates or continues a conversation in the shared inbox based on the From number
- Inbound MMS: images and media downloaded from Twilio/Vonage media URLs and stored via spatie/laravel-media-library
- Outbound SMS: agents reply from the inbox. Message sent via the provider API using the channel's number as the `From`.
- Delivery status webhooks: Twilio/Vonage deliver status callbacks (`delivered`, `failed`, `undelivered`) update `inbox_messages.delivered_at` and `delivery_status`.
- STOP opt-out: inbound message matching `STOP`, `UNSUBSCRIBE`, `CANCEL`, `END`, `QUIT` (case-insensitive) triggers opt-out — adds record to `inbox_sms_opt_outs`, auto-replies with a compliance message, blocks future outbound sends to that number.
- HELP keyword: inbound `HELP` triggers an automatic SMS reply with the company's opt-out instructions and support contact.

### Advanced
- SMS templates: pre-written message templates with variable substitution (`{{first_name}}`, `{{company_name}}`). Agents select from templates when composing. Useful for standard outbound notifications.
- TCPA/GDPR consent tracking: before any outbound SMS is sent, the system checks `inbox_sms_consents` for explicit opt-in evidence (source, timestamp, IP). If no consent record exists, the send is blocked and the agent is prompted to record consent first.
- Consent recording: agents can manually record consent (e.g. "customer provided verbal consent on phone call — date X, agent Y"). Source and notes stored.
- Number provisioning: search for and provision new phone numbers via Twilio/Vonage API directly from Filament (area code search, toll-free, local).
- Long code vs short code support: standard long-code numbers for two-way conversation; short codes for high-volume one-way campaigns (though campaigns belong to the Marketing domain).
- Segment awareness: automatically splits messages over 160 characters into segments and shows segment count to agent before sending to manage cost.

### AI-Powered
- SMS-optimised replies: Claude drafts replies calibrated for SMS length limits — concise, no HTML, natural language
- Opt-in collection message drafting: AI drafts a TCPA-compliant opt-in request message given the company's product description and tone settings

## Data Model

```erDiagram
    inbox_channels {
        ulid id PK
        ulid company_id FK
        string type
        string name
        json credentials_encrypted
        boolean is_active
        string webhook_secret
        timestamps created_at/updated_at
    }

    inbox_sms_configs {
        ulid id PK
        ulid channel_id FK
        string provider
        string phone_number
        string account_sid
        timestamps created_at/updated_at
    }

    inbox_sms_opt_outs {
        ulid id PK
        ulid company_id FK
        ulid channel_id FK
        string phone_number
        timestamp opted_out_at
        string keyword_used
        timestamps created_at/updated_at
    }

    inbox_sms_consents {
        ulid id PK
        ulid company_id FK
        ulid channel_id FK
        string phone_number
        string source
        string ip_address
        text notes
        ulid recorded_by FK
        timestamp consented_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `provider` | twilio / vonage |
| `credentials_encrypted` | Encrypted JSON: Twilio — `{ account_sid, auth_token, phone_number_sid }`. Vonage — `{ api_key, api_secret }` |
| `keyword_used` | The exact keyword the contact replied (e.g. STOP, UNSUBSCRIBE) — for compliance records |
| `source` | How consent was obtained: web-form / verbal / sms-keyword / import / api |
| `phone_number` on opt-outs and consents | E.164 normalised (e.g. `+31612345678`) |

## Permissions

```
inbox.sms.view
inbox.sms.send
inbox.sms.configure
inbox.sms.consent-manage
inbox.sms.opt-out-manage
```

## Filament

- **Resource:** `ChannelResource` (shared — see WhatsApp Channel)
- **Custom pages:** `SmsChannelSetupWizard` — Step 1 choose provider (Twilio / Vonage), Step 2 enter API credentials, Step 3 select or provision phone number (queries provider API), Step 4 configure webhook (shows URL to paste into provider console), Step 5 compliance settings (default opt-out response message). `SmsConsentResource` — standard CRUD for viewing and recording consent records. `SmsOptOutResource` — read-only list of opted-out numbers with export action.
- **Widgets:** None specific — channel status in shared `ChannelResource` list.
- **Nav group:** Channels (inbox panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| SimpleTexting | Two-way SMS business messaging |
| SlickText | SMS conversations and opt-out management |
| Attentive | Business SMS, compliance |
| Respond.io | SMS channel in omnichannel inbox |
| OpenPhone | Business SMS inbox |

## Related

- [[shared-inbox]]
- [[whatsapp-channel]]
- [[email-channel]]
- [[inbox-automations]]

## Implementation Notes

- **Twilio inbound webhook:** Configure in Twilio Console → Phone Numbers → Messaging → Webhook URL set to `https://app.flowflex.io/webhooks/sms/twilio/{webhook_secret}`. Inbound POST contains `From`, `To`, `Body`, `MessageSid`, and media URL parameters for MMS. `TwilioSmsWebhookController` validates the `X-Twilio-Signature` HMAC before processing.
- **Vonage inbound webhook:** Configure Vonage SMS inbound webhook in Vonage Dashboard. POST payload includes `msisdn` (from), `to`, `text`, `messageId`. `VonageSmsWebhookController` handles validation and processing.
- **Opt-out interception:** Both controllers check whether the `Body` matches the opt-out keyword list (case-insensitive) before creating a conversation message. On match: creates opt-out record, sends compliance reply (via provider API directly, not through the inbox), and marks the conversation as resolved. Does not create an inbox message for the opt-out keyword itself — keeps conversation thread clean.
- **Consent gate:** `SmsAdapter::send()` calls `ConsentChecker::hasConsent($phoneNumber, $channelId)` before every outbound send. Returns false if no `inbox_sms_consents` record exists for that number on that channel. On false: throws `SmsConsentRequiredException`, which `InboxPage` catches and renders as a Filament modal prompting the agent to record consent first.
- **E.164 normalisation:** All phone numbers stored and compared in E.164 format. Inbound numbers normalised using `libphonenumber-for-php` (Giggsey port) in `PhoneNormaliser` service. Detected locale used for numbers without country prefix (falls back to company's default country setting).
