---
type: module
domain: Omnichannel Inbox
panel: inbox
module-key: inbox.whatsapp
status: planned
color: "#4ADE80"
---

# WhatsApp Channel

> Connect WhatsApp Business API (Meta Cloud API) to the shared inbox — inbound/outbound messages, template-based outbound initiation, media support, delivery/read receipts, and opt-out management.

**Panel:** `/inbox`
**Module key:** `inbox.whatsapp`

## What It Does

WhatsApp Channel connects a company's WhatsApp Business number to the FlowFlex shared inbox via the Meta WhatsApp Business Cloud API. All inbound WhatsApp messages from customers create or continue conversations in the shared inbox where agents respond. When initiating a conversation with a customer (outside the 24-hour customer service window enforced by Meta), agents must use pre-approved WhatsApp Message Templates. The module handles media — images, documents, audio files, and voice notes — both inbound and outbound. Delivery and read receipts from Meta are reflected on each message so agents know when their reply was delivered and read. Opt-out messages (customers replying STOP) are tracked and respected to maintain compliance.

## Features

### Core
- Connect WhatsApp Business number via Meta Cloud API. Credentials (phone number ID, business account ID, access token) stored encrypted in `inbox_channels.credentials_encrypted`.
- Webhook receiver at `/webhooks/whatsapp/{verify_token}` — handles `GET` challenge verification and `POST` message events
- Inbound: receive text messages, images, documents, audio, video, stickers, location messages
- Outbound: send text replies within the 24-hour customer service window. Messages outside the window must use approved Templates.
- WhatsApp Message Templates: create, submit for Meta review, and use approved templates for outbound-initiated messages. Variable fields in templates filled at send time.
- Delivery receipts: `delivered_at` updated on `inbox_messages` when Meta sends a `message_status: delivered` webhook event
- Read receipts: `read_at` updated when Meta sends `message_status: read`
- Opt-out: customers who message STOP are flagged in `inbox_whatsapp_opt_outs`. Outbound sends to opted-out numbers are blocked with an agent-visible warning.

### Advanced
- Multiple WhatsApp numbers per company: each number is a separate `inbox_channel` record. Conversations are labelled by which number they arrived on.
- Media download and storage: inbound media URLs (from Meta API) are downloaded via a queued `DownloadWhatsAppMedia` job and stored in Laravel's configured disk (S3) via spatie/laravel-media-library. Original Meta URLs expire in 5 minutes — download must happen immediately.
- Template variable preview: when composing a template message, agent fills variable values and sees a rendered preview before sending
- Failed message retry: if outbound message delivery fails (rate limit, network error), the system retries up to 3 times with exponential back-off via a queued job
- Conversation labels from WhatsApp: if incoming messages contain WhatsApp catalogue or order data, auto-label the conversation with the relevant type

### AI-Powered
- Smart template selection: when an agent needs to initiate a conversation and must use a template, Claude analyses the conversation context and suggests the most appropriate approved template with pre-filled variable values
- WhatsApp-optimised reply drafts: AI drafts replies formatted for WhatsApp conventions (concise, no HTML, emoji-aware)

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

    inbox_whatsapp_configs {
        ulid id PK
        ulid channel_id FK
        string phone_number_id
        string business_account_id
        string verify_token
        json approved_templates
        timestamps created_at/updated_at
    }

    inbox_whatsapp_opt_outs {
        ulid id PK
        ulid company_id FK
        ulid channel_id FK
        string phone_number
        timestamp opted_out_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` | whatsapp / email / sms / instagram / facebook — `inbox_channels` is polymorphic parent |
| `credentials_encrypted` | JSON encrypted with `encrypt()` — contains `access_token`, `phone_number_id`, `business_account_id` |
| `webhook_secret` | Random token embedded in webhook URL — validates inbound webhooks from Meta |
| `approved_templates` | JSON snapshot of templates approved in Meta Business Manager, synced via API on demand |
| `verify_token` | Static token for Meta webhook `GET` challenge verification |

## Permissions

```
inbox.whatsapp.view
inbox.whatsapp.send
inbox.whatsapp.configure
inbox.whatsapp.templates
inbox.whatsapp.opt-outs
```

## Filament

- **Resource:** `ChannelResource` — manages all inbox channels (WhatsApp, Email, SMS, Social) in a unified resource with a `type` filter. Shows channel name, type badge, active status, and conversation count.
- **Pages:** `ListChannels`, `CreateChannel` (type selector wizard), `EditChannel`
- **Custom pages:** `WhatsAppSetupWizard` — multi-step Filament Wizard page: Step 1 enter Meta credentials, Step 2 verify phone number via Meta API test call, Step 3 configure webhook (shows the webhook URL to paste into Meta Business Manager), Step 4 sync templates. Class: `App\Filament\Inbox\Pages\WhatsAppSetupWizard`.
- **Widgets:** None — channel status shown in `ChannelResource` list view. Overall channel health in `InboxSummaryWidget` on the dashboard.
- **Nav group:** Channels (inbox panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Wati | WhatsApp Business API management |
| Interakt | WhatsApp shared inbox |
| 360dialog | WhatsApp API connection |
| Respond.io | WhatsApp channel in omnichannel inbox |
| Bird (MessageBird) | WhatsApp Business API |

## Related

- [[shared-inbox]]
- [[inbox-automations]]
- [[email-channel]]
- [[sms-channel]]
- [[social-inbox]]

## Implementation Notes

- **Meta Cloud API:** Uses Meta's WhatsApp Business Cloud API (v17.0+). All API calls go to `https://graph.facebook.com/v17.0/{phone_number_id}/messages`. Authentication via Bearer token (System User access token from Meta Business Manager — does not expire unless revoked).
- **Webhook processing:** `WhatsAppWebhookController` at `/webhooks/whatsapp/{verify_token}`. Handles: `GET` (challenge echo), `POST` (message events). On POST, validates `X-Hub-Signature-256` header against HMAC-SHA256 of payload using the channel's `webhook_secret`. Dispatches `ProcessWhatsAppWebhook` queued job immediately to avoid webhook timeout (Meta expects < 200ms acknowledgement). Job parses the message object, finds or creates the `inbox_conversation`, and creates the `inbox_message`.
- **24-hour window enforcement:** The `WhatsAppAdapter::send()` method checks whether the last inbound message from the contact on this conversation was within 24 hours. If within window: sends free-form text. If outside window: requires a template. If an agent attempts to send free-form text outside the window, a Filament notification error is shown and the send is blocked with a prompt to select a template.
- **Media handling:** Meta media URLs in webhook payloads are valid for only 5 minutes. `DownloadWhatsAppMedia` job is dispatched immediately with high priority, downloads the file using the media URL + access token, stores it via spatie/laravel-media-library, and updates the message attachment JSON with the permanent FlowFlex storage URL.
- **Template sync:** `SyncWhatsAppTemplates` command queries the Meta Graph API `GET /{business_account_id}/message_templates` and updates `inbox_whatsapp_configs.approved_templates`. Run on-demand via a Filament Action on the channel edit page, and nightly via scheduler.
