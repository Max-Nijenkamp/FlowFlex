---
type: domain-index
domain: Marketing
panel: marketing
color: "#4ADE80"
---

# Marketing

Email campaigns, drip sequences, forms, landing pages, content CMS, and attribution analytics. **Panel:** `/marketing` (Pink) — Phase 3.

**Displaces**: Mailchimp, ActiveCampaign, Brevo, HubSpot Marketing

---

## Navigation Groups

- **Campaigns** — Campaigns, Email Sequences
- **Capture** — Forms, Landing Pages
- **Content** — Blog Posts, Categories
- **Analytics** — Marketing Dashboard, UTM Builder

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/marketing/campaigns\|Campaigns]] | `marketing.campaigns` | planned | **P3 core** |
| [[domains/marketing/forms\|Forms]] | `marketing.forms` | planned | **P3 core** |
| [[domains/marketing/email-sequences\|Email Sequences]] | `marketing.sequences` | planned | P3 |
| [[domains/marketing/landing-pages\|Landing Pages]] | `marketing.landing-pages` | planned | P3 |
| [[domains/marketing/content-cms\|Content CMS]] | `marketing.cms` | planned | P3 |
| [[domains/marketing/marketing-analytics\|Marketing Analytics]] | `marketing.analytics` | planned | P3 |
| [[domains/marketing/utm-tracking\|UTM Tracking]] | `marketing.utm` | planned | P3 |

---

## Key Patterns

- Batched queue sends (campaigns, sequences) — see [[architecture/queue-jobs]]
- `spatie/laravel-sluggable` — landing pages, blog posts
- `awcodes/filament-tiptap-editor` — campaign + post content
- Public rendering (landing pages, blog) via Vue + Inertia — see [[frontend/_index]]
- Cross-domain: `FormSubmissionReceived` → CRM contact creation
- Pulls audiences from [[domains/crm/customer-segments]]
