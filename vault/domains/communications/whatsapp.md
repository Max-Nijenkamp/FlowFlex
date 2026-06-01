---
type: module
domain: Communications
panel: comms
module-key: comms.whatsapp
status: planned
color: "#4ADE80"
---

# WhatsApp

WhatsApp Business API channel — send and receive WhatsApp messages, manage approved message templates, and broadcast. **The key EU SMB differentiator** (see [[product/positioning]]).

## Core Features

- WhatsApp Business API integration via provider (360dialog recommended — see ADR needed)
- Receive inbound WhatsApp messages into the shared inbox
- Send outbound messages (within 24h customer service window)
- Template messages: pre-approved templates for messages outside the 24h window
- Template management: create, submit for approval, track approval status
- Template variables: `{{1}}`, `{{2}}` placeholder substitution
- Media messages: send/receive images, documents, location
- Phone numbers stored E.164 via `propaganistas/laravel-phone`
- Delivery + read receipts
- WhatsApp number registration and verification flow

## Data Model

| Table | Key Columns |
|---|---|
| `comms_whatsapp_templates` | company_id, name, category, language, body, variables (json), approval_status, external_template_id |
| `comms_whatsapp_config` | company_id, phone_number_e164, provider, api_key (encrypted), business_account_id |

Messages flow through `comms_messages` (channel_type = whatsapp).

## Filament

**Nav group:** Inbox / Settings

- `WhatsAppTemplateResource` — create, submit, track template approval
- `WhatsAppConfigPage` (custom page) — connect number, enter API credentials
- Sending happens through the Shared Inbox composer

## Decisions Needed

- **ADR**: WhatsApp provider choice — 360dialog vs Twilio vs Meta Cloud API direct. Log via `/flowflex:decision` when building.

## Cross-Domain / Security

- API key encrypted at rest (see [[architecture/patterns/encryption]])
- Inbound messages via provider webhook → verify signature (see [[architecture/security]])

## Related

- [[domains/communications/shared-inbox]]
- [[domains/communications/broadcast]]
- [[product/positioning]]
