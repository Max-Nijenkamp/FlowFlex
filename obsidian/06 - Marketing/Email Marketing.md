---
tags: [flowflex, domain/marketing, email-marketing, campaigns, phase/5]
domain: Marketing & Content
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-07
---

# Email Marketing

Campaign builder, automation sequences, A/B testing, and deliverability tools. Replaces Mailchimp.

**Who uses it:** Marketing team
**Filament Panel:** `marketing`
**Depends on:** Core, [[Contact & Company Management]]
**Phase:** 5
**Build complexity:** Very High — 3 resources, 3 pages, 8 tables

---

## Features

- **Campaign builder** — compose one-off broadcast emails to lists or segments
- **Drag-and-drop email editor** — blocks: text, image, button, divider, columns, spacer, social icons
- **Automation sequences** — multi-step email series triggered by events (welcome, abandoned cart, CSAT, win-back, re-engagement)
- **A/B testing** — split campaigns on subject line or body content; auto-select winner by open rate
- **Audience segments** — dynamic segments based on CRM contact fields, tags, or behaviour
- **List management** — subscribe, unsubscribe, suppression list; GDPR double opt-in support
- **Scheduling** — send immediately or schedule for a future date/time; per-timezone delivery (send at 9am local time)
- **Open / click analytics** — per campaign: open rate, click rate, bounces, unsubscribes, revenue attributed
- **Link tracking** — auto-wrap all links with tracking redirects
- **Unsubscribe and bounce handling** — automatic suppression; one-click unsubscribe in compliance with CAN-SPAM and GDPR
- **Deliverability tools** — DKIM, SPF, DMARC configuration checker and status badge
- **Transactional email** — branded receipts, password resets (using same template engine)
- **Template library** — save and reuse email designs; import HTML templates
- **Spam score check** — SpamAssassin-style check before send

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `email_campaigns`
| Column | Type | Notes |
|---|---|---|
| `name` | string | internal name |
| `subject` | string | |
| `preview_text` | string nullable | email preview/preheader |
| `from_name` | string | |
| `from_email` | string | |
| `reply_to` | string nullable | |
| `body_html` | longtext | rendered HTML |
| `body_json` | json | editor block structure |
| `status` | enum | `draft`, `scheduled`, `sending`, `sent`, `cancelled` |
| `scheduled_at` | timestamp nullable | |
| `sent_at` | timestamp nullable | |
| `ab_variant_of_id` | ulid FK nullable | → email_campaigns |
| `ab_traffic_split` | integer nullable | % of audience |
| `ab_winner_id` | ulid FK nullable | → email_campaigns |

### `campaign_recipients`
| Column | Type | Notes |
|---|---|---|
| `email_campaign_id` | ulid FK | → email_campaigns |
| `crm_contact_id` | ulid FK | → crm_contacts |
| `status` | enum | `pending`, `sent`, `opened`, `clicked`, `bounced`, `unsubscribed` |
| `sent_at` | timestamp nullable | |
| `opened_at` | timestamp nullable | |
| `clicked_at` | timestamp nullable | |
| `bounced_at` | timestamp nullable | |
| `bounce_reason` | string nullable | |

### `email_sequences`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `trigger` | enum | `contact_created`, `form_submitted`, `ticket_resolved`, `checkout_completed`, `cart_abandoned`, `custom_event` |
| `trigger_filter` | json nullable | additional conditions for trigger |
| `is_active` | boolean | default true |

### `sequence_steps`
| Column | Type | Notes |
|---|---|---|
| `email_sequence_id` | ulid FK | → email_sequences |
| `delay_days` | integer | delay after previous step |
| `delay_hours` | integer | additional hours |
| `subject` | string | |
| `body_html` | longtext | |
| `body_json` | json | |
| `sort_order` | integer | |

### `sequence_enrollments`
| Column | Type | Notes |
|---|---|---|
| `email_sequence_id` | ulid FK | → email_sequences |
| `crm_contact_id` | ulid FK | → crm_contacts |
| `current_step` | integer | default 0 |
| `status` | enum | `active`, `completed`, `cancelled` |
| `enrolled_at` | timestamp | |
| `completed_at` | timestamp nullable | |

### `email_templates`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `subject` | string nullable | |
| `body_html` | longtext | |
| `body_json` | json | |
| `is_global` | boolean | available to all company campaigns |

### `email_unsubscribes`
| Column | Type | Notes |
|---|---|---|
| `email` | string | normalised email |
| `reason` | enum | `unsubscribed`, `bounced`, `complained`, `manual` |
| `source` | string nullable | campaign or sequence that triggered |
| `unsubscribed_at` | timestamp | |

### `email_campaign_stats`
| Column | Type | Notes |
|---|---|---|
| `email_campaign_id` | ulid FK | → email_campaigns |
| `total_sent` | integer | default 0 |
| `total_opened` | integer | default 0 |
| `total_clicked` | integer | default 0 |
| `total_bounced` | integer | default 0 |
| `total_unsubscribed` | integer | default 0 |
| `open_rate` | decimal(5,4) | computed |
| `click_rate` | decimal(5,4) | computed |
| `computed_at` | timestamp | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `EmailCampaignSent` | `email_campaign_id`, `total_recipients` | Analytics (track campaign stats) |
| `ContactUnsubscribed` | `crm_contact_id`, `email` | CRM (flag contact as unsubscribed) |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `FormSubmissionReceived` | [[Forms & Lead Capture]] | Enroll contact in triggered sequence |
| `TicketResolved` | [[Customer Support & Helpdesk]] (Phase 3) | Trigger CSAT survey sequence |
| `CheckoutCompleted` | [[Storefront & Checkout]] (Phase 4) | Trigger post-purchase sequence |
| `CartAbandoned` | [[Storefront & Checkout]] (Phase 4) | Trigger abandoned cart sequence |

---

## Permissions

```
marketing.email.campaigns.view
marketing.email.campaigns.create
marketing.email.campaigns.edit
marketing.email.campaigns.delete
marketing.email.campaigns.send
marketing.email.sequences.view
marketing.email.sequences.create
marketing.email.sequences.edit
marketing.email.sequences.delete
marketing.email.templates.view
marketing.email.templates.manage
marketing.email.analytics.view
```

---

## Related

- [[Marketing Overview]]
- [[Forms & Lead Capture]]
- [[Contact & Company Management]]
- [[Customer Data Platform]]
