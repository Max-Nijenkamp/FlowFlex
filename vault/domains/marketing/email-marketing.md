---
type: module
domain: Marketing
panel: marketing
module-key: marketing.email
status: planned
color: "#4ADE80"
---

# Email Marketing

> Build, send, and track email campaigns to segmented contact lists with full open, click, and unsubscribe analytics.

**Panel:** `marketing`
**Module key:** `marketing.email`

## What It Does

Email Marketing replaces standalone tools like Mailchimp for outbound marketing email. It covers the full workflow: build a template with a drag-and-drop editor, select a recipient list segment, schedule or send immediately, and track opens, clicks, and unsubscribes per campaign. Lists sync from CRM contacts and lead capture forms so there is no manual CSV import loop.

## Features

### Core
- Drag-and-drop email template editor: blocks for text, image, button, divider, social icons, HTML
- Template library: welcome email, newsletter, product announcement, webinar invite, promotional
- Recipient list management: create lists, add contacts manually or from CRM segment
- Send and schedule: send now or schedule for a future date/time
- Unsubscribe handling: one-click unsubscribe link auto-injected; compliance with GDPR/CAN-SPAM
- Deliverability: SPF/DKIM configuration per sending domain, bounce handling, suppression list management
- Per-send analytics: delivered, opened (unique + total), clicked (unique + total), unsubscribed, bounced

### Advanced
- Personalisation tokens: `{{first_name}}`, `{{company}}`, custom CRM fields
- Conditional content blocks: show different content based on contact segment or property
- Send-time optimisation: AI picks best send hour per recipient based on past open history
- Automated sequences: drip campaigns triggered by contact event (form fill, tag applied, deal stage)
- List hygiene: automatic suppression of hard bounces; re-engagement campaigns for cold contacts
- Multi-variant sends: A/B test subject lines, from names, or content with automatic winner promotion

### AI-Powered
- Subject line generator: suggest 5 subject line variants optimised for open rate
- Content assistant: write email body copy from a brief prompt
- Spam score checker: predict inbox vs spam placement before sending

## Data Model

```erDiagram
    mkt_email_templates {
        ulid id PK
        ulid company_id FK
        string name
        string subject
        string from_name
        string from_email
        json blocks
        string preview_text
        timestamps timestamps
    }

    mkt_email_lists {
        ulid id PK
        ulid company_id FK
        string name
        string source
        integer contact_count
        timestamps timestamps
    }

    mkt_email_sends {
        ulid id PK
        ulid company_id FK
        ulid template_id FK
        ulid list_id FK
        ulid campaign_id FK
        string status
        timestamp scheduled_at
        timestamp sent_at
        integer total_recipients
        integer delivered
        integer opened
        integer clicked
        integer unsubscribed
        integer bounced
    }

    mkt_email_events {
        ulid id PK
        ulid send_id FK
        ulid contact_id FK
        string event_type
        string link_url
        timestamp occurred_at
    }

    mkt_email_sends ||--o{ mkt_email_events : "tracks"
    mkt_email_lists }o--o{ mkt_email_sends : "used in"
```

| Table | Purpose |
|---|---|
| `mkt_email_templates` | Reusable email designs |
| `mkt_email_lists` | Recipient lists |
| `mkt_email_sends` | Individual sends with aggregate stats |
| `mkt_email_events` | Per-contact open/click/unsub events |

## Permissions

```
marketing.email.view-any
marketing.email.create
marketing.email.send
marketing.email.manage-lists
marketing.email.view-analytics
```

## Filament

**Resource class:** `EmailCampaignResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `EmailTemplateBuilderPage` (WYSIWYG block editor), `EmailAnalyticsPage` (per-send stats)
**Widgets:** `EmailPerformanceWidget` (recent send stats overview)
**Nav group:** Campaigns

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Mailchimp | Templates, lists, sends, analytics |
| Klaviyo | Email + automation for product businesses |
| Campaign Monitor | Email design and sending |
| ActiveCampaign (email) | List management and drip sequences |

## Implementation Notes

**External dependency — email sending provider (must be decided before build):** The spec describes building, sending, and tracking emails. The sending infrastructure is NOT described. Options:

1. **Amazon SES** (`aws/aws-sdk-php`): Very low cost (~$0.10/1,000 emails). No built-in open/click tracking — must implement tracking pixels and redirect links in FlowFlex. Requires SPF/DKIM domain verification in SES console.
2. **Mailgun** (`mailgun/mailgun-php`): Has built-in open/click webhook events. ~$15/month for 50,000 emails. Easy to implement — Mailgun fires webhooks to FlowFlex for open/click events.
3. **SendGrid** (`sendgrid/sendgrid-php`): Similar to Mailgun. Has its own event webhook for opens/clicks.
4. **Brevo (formerly Sendinblue)**: Also webhook-based event tracking.

**Recommended:** Use **Laravel's mail driver abstraction** with the chosen provider set via `MAIL_MAILER` in `.env`. For open/click tracking, Mailgun or SendGrid provide webhook delivery of events which is simpler than self-implementing tracking pixel infrastructure. Configure `MAIL_MAILER=mailgun` and `MAILGUN_SECRET`, `MAILGUN_DOMAIN` in `.env`.

**Bulk send job:** Email campaign sends are dispatched as `SendCampaignJob` using `Bus::batch()`. Each job in the batch sends to one recipient (or a chunk of 50) using the Mail facade. Rate limit: most ESP limits are 100/second — enforce via `RateLimiter::attempt('email-send', 100, fn() => null, 1)` in the job's `handle()` method.

**Open/click tracking (if using Mailgun/SendGrid webhooks):** Register a webhook endpoint `POST /webhooks/mailgun` (or SendGrid equivalent) that receives event payloads and updates `mkt_email_events`. The endpoint must be excluded from CSRF middleware (add to `$except` in `VerifyCsrfToken.php`). Verify webhook signatures to prevent spoofing.

**GDPR/CAN-SPAM compliance:** One-click unsubscribe link is auto-injected into every email body. Unsubscribes are stored in a `mkt_email_unsubscribes {ulid id, ulid company_id, string email, timestamp unsubscribed_at}` table — not currently defined. Add it. The send job checks this table before sending each recipient. `LIST-UNSUBSCRIBE` header must be set with a mailto: address that auto-processes unsubscribes.

**Drag-and-drop email builder (`EmailTemplateBuilderPage`):** This is a custom Filament `Page`. The block editor stores template content as a JSON `blocks` array (same concept as LMS `course_lessons.blocks`). For email, use **Unlayer** (third-party embeddable email editor, free tier available) or build a custom block editor with Livewire components. Unlayer has a JavaScript SDK that embeds in a `<div>` and exports HTML + JSON. Recommended for MVP — reduces build time significantly. Add Unlayer embed script to the `EmailTemplateBuilderPage` Blade view.

**AI features:** Subject line generator and content assistant both call `app/Services/AI/EmailMarketingService.php` wrapping OpenAI GPT-4o. Spam score checker uses a heuristic PHP library (`ddeboer/iconv-stream-filter` or similar) combined with a simple word/pattern blacklist — or optionally calls the Mail-Tester API.

## Related

- [[campaigns]] — email sends belong to campaigns
- [[lead-capture]] — form submissions add contacts to lists
- [[a-b-testing]] — subject line and content A/B tests
- [[analytics]] — email attribution in conversion funnels
- [[../crm/INDEX]] — contact and segment data source
