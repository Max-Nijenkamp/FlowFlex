---
tags: [flowflex, domain/marketing, sms, whatsapp, phase/5]
domain: Marketing
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-08
---

# SMS & WhatsApp Marketing

Send marketing messages where customers actually read them — their phone. SMS open rates are 98%; email is 20%. Add WhatsApp Business API for rich messages: images, buttons, carousels. Replaces Twilio Studio + Klaviyo SMS.

**Who uses it:** Marketing teams, ecommerce managers, customer success
**Filament Panel:** `marketing`
**Depends on:** Core, [[Email Marketing]], [[Contact & Company Management]], Twilio (SMS), WhatsApp Business API
**Phase:** 5

---

## Features

### SMS Campaigns

- Broadcast SMS to contact lists/segments
- Personalisation tokens: {{first_name}}, {{order_number}}, {{promo_code}}
- Character count with MMS/SMS boundary indicator (160 chars)
- URL shortener: long URLs auto-shortened + click-tracked
- Send time optimisation: AI picks best send time per recipient timezone
- Opt-out handling: reply STOP → auto-unsubscribed, never messaged again
- Two-way SMS: receive replies → creates CRM conversation thread

### WhatsApp Business Messaging

- Official WhatsApp Business API via Twilio/360dialog
- Template messages (required by Meta for outbound): pre-approved message templates
- Rich messages: image + caption, PDF document, location share
- Interactive messages: reply buttons (up to 3), list picker (up to 10 options)
- Product catalogue messages: show product card directly in WhatsApp
- Broadcast lists (WhatsApp approved segments — must have opted-in contacts)
- Conversational replies → routed to [[Shared Inbox & Email]] or [[External Chat Widget]]

### Campaign Builder

- Drag-and-drop flow builder for multi-step SMS sequences
- Triggers: form submission, purchase, abandoned cart, event date
- Delays: send follow-up after 2 hours, 1 day, 3 days
- Branch by: replied / did not reply, clicked link / did not click
- Stop on conversion: if purchase made, cancel remaining sequence

### Contact Lists & Segments

- Uses same contact segments as Email Marketing (no duplicating)
- SMS-specific filter: has mobile number + SMS opt-in
- WhatsApp filter: has WhatsApp-enabled number + WhatsApp opt-in
- GDPR double opt-in: keyword SUBSCRIBE → confirmation message → subscribed
- Imported lists: CSV upload with explicit consent declaration checkbox

### Analytics

- Delivery rate, failure reasons (invalid number, carrier blocked)
- Open rate (MMS), click rate per link
- Opt-out rate, opt-out reasons
- Revenue attributed: contacts who clicked → purchased within 24h
- Cost per campaign (Twilio credit consumed)

### Compliance

- GDPR: separate opt-in stored per channel (email ≠ SMS ≠ WhatsApp)
- TCPA (US): quiet hours enforcement (no sends 9pm–8am local time)
- Dutch telecommunications law: opt-in required for all promotional SMS
- Auto-append opt-out instruction on first message to new contacts

---

## Database Tables (3)

### `marketing_sms_campaigns`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `channel` | enum | `sms`, `whatsapp` |
| `message_template` | text | with {{tokens}} |
| `media_file_id` | ulid FK nullable | for MMS/WhatsApp |
| `segment_id` | ulid FK nullable | |
| `scheduled_at` | timestamp nullable | |
| `sent_count` | integer default 0 | |
| `delivered_count` | integer default 0 | |
| `failed_count` | integer default 0 | |
| `click_count` | integer default 0 | |
| `optout_count` | integer default 0 | |
| `status` | enum | `draft`, `scheduled`, `sending`, `sent` |

### `marketing_sms_sends`
| Column | Type | Notes |
|---|---|---|
| `campaign_id` | ulid FK | |
| `contact_id` | ulid FK | → crm_contacts |
| `phone_number` | string | |
| `status` | enum | `queued`, `sent`, `delivered`, `failed`, `optout` |
| `provider_message_id` | string nullable | Twilio SID |
| `sent_at` | timestamp nullable | |
| `delivered_at` | timestamp nullable | |

### `marketing_sms_optouts`
| Column | Type | Notes |
|---|---|---|
| `phone_number` | string | |
| `channel` | enum | `sms`, `whatsapp` |
| `opted_out_at` | timestamp | |
| `reason` | string nullable | STOP reply, manual, etc. |

---

## Permissions

```
marketing.sms.view
marketing.sms.create
marketing.sms.send
marketing.sms.manage-optouts
marketing.sms.view-analytics
```

---

## Competitor Comparison

| Feature | FlowFlex | Klaviyo SMS | Twilio Studio | Messagebird |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€20+/mo) | ❌ (pay per SMS) | ❌ |
| WhatsApp Business API | ✅ | ❌ | ✅ | ✅ |
| Integrated with CRM contacts | ✅ | ✅ | ❌ | ❌ |
| Two-way → CRM inbox | ✅ | ❌ | ✅ | partial |
| GDPR double opt-in | ✅ | ✅ | manual | ✅ |
| Drag-and-drop flow builder | ✅ | ✅ | ✅ | ✅ |

---

## Related

- [[Marketing Overview]]
- [[Email Marketing]]
- [[Contact & Company Management]]
- [[External Chat Widget]]
- [[Shared Inbox & Email]]
