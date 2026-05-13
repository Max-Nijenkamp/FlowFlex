---
type: domain-index
domain: Omnichannel Inbox
panel: inbox
panel-path: /inbox
panel-color: Green
color: "#4ADE80"
---

# Omnichannel Inbox

Omnichannel Inbox is the external customer messaging domain of FlowFlex. It provides a single shared inbox where agents manage conversations arriving from all connected external channels — WhatsApp, email, SMS, Instagram DM, and Facebook Messenger — without switching between separate apps. The Inbox panel lives at `/inbox`.

**Important distinction:** This domain handles unstructured, real-time external channel conversations. It is different from the **Support & Help Desk** domain, which handles structured SLA-tracked ticket workflows, and from the **Communications** domain, which handles internal team messaging. Conversations in the Inbox can be escalated to Support Tickets but are managed separately.

## Navigation Groups

- **Inbox** — Shared Inbox (main workspace)
- **Channels** — WhatsApp, Email, SMS, Social (Instagram/Facebook)
- **Automation** — Inbox Automations
- **Settings** — Analytics, Channel Settings

## Modules

| Module | File | Module Key | Description |
|---|---|---|---|
| Shared Inbox | [[shared-inbox]] | `inbox.shared` | Central three-panel UI for all channels: contact list, thread, sidebar. Real-time via Reverb. |
| WhatsApp Channel | [[whatsapp-channel]] | `inbox.whatsapp` | WhatsApp Business API, templates, media, delivery receipts, opt-out management |
| Email Channel | [[email-channel]] | `inbox.email` | Support email addresses → inbox conversations. Mailgun/SES inbound + IMAP polling. |
| SMS Channel | [[sms-channel]] | `inbox.sms` | Twilio/Vonage two-way SMS, MMS, opt-out handling, TCPA/GDPR consent tracking |
| Social Inbox | [[social-inbox]] | `inbox.social` | Instagram DM, Facebook Messenger, story mentions, comment monitoring via Meta Graph API |
| Inbox Automations | [[inbox-automations]] | `inbox.automations` | IF/THEN rules: auto-assign, auto-label, first-response template, escalation, close inactive |
| Inbox Analytics | [[inbox-analytics]] | `inbox.analytics` | Volume by channel, response time, CSAT, agent workload, busiest hours heatmap |

## Primary Displaces

Respond.io, Chatwoot, Freshdesk Messaging, Zendesk Messaging, Bird (MessageBird)

## Related

- [[domains/support/INDEX]] — Support & Help Desk (structured ticket workflows, SLAs)
- [[domains/communications/INDEX]] — Communications (internal team messaging)
- [[domains/crm/INDEX]] — CRM & Sales (contacts linked to conversations)
- [[architecture/filament-patterns]]
- [[architecture/module-system]]
