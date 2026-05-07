---
tags: [flowflex, domain/marketing, overview, phase/5]
domain: Marketing & Content
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-07
---

# Marketing Overview

CMS, email campaigns, forms, social media, SEO, ads, events, and affiliate management. All 8 modules built in Phase 5 as a complete panel.

**Filament Panel:** `marketing`
**Domain Colour:** Pink `#DB2777` / Light: `#FCE7F3`
**Domain Icon:** `heroicon-o-megaphone`
**Phase:** 5 — complete domain, all modules

## Modules

| Module | Description |
|---|---|
| [[CMS & Website Builder]] | Content pages, blocks, media library, redirects, SEO, scheduled publishing |
| [[Email Marketing]] | Campaign builder, recipients, sequences, A/B testing, analytics, deliverability |
| [[Forms & Lead Capture]] | Form builder (all field types, conditional logic), submissions, CRM auto-create |
| [[Social Media Management]] | Multi-platform publishing, content calendar, post analytics |
| [[SEO & Analytics]] | SEO audits, keyword rankings, GA4 snapshots, redirect manager |
| [[Ad Campaign Management]] | Google/Meta/LinkedIn/TikTok ad accounts, campaign tracking, ROAS |
| [[Events & Webinars]] | Event management, registrations, sessions, waitlist, QR check-in |
| [[Affiliate & Partner Management]] | Affiliates, referral tracking, commissions, payouts |

## Filament Panel Structure

**Navigation Groups:**
- `Content` — Content Pages, Media Library, Redirects
- `Campaigns` — Email Campaigns, Email Sequences, Social Posts, Ad Campaigns
- `Capture` — Forms, Form Submissions
- `Events` — Events, Registrations
- `Partners` — Affiliates, Referrals, Payouts
- `Insights` — SEO Audits, Keyword Rankings, Ad Performance

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `FormSubmissionReceived` | Forms | CRM (auto-create contact), Email (trigger sequence) |
| `EmailCampaignSent` | Email Marketing | Analytics (track stats) |
| `SocialPostPublished` | Social | Analytics (track engagement) |
| `SocialPostFailed` | Social | Notifications (alert team) |
| `CampaignBudgetExhausted` | Ad Campaigns | Notifications (alert marketing manager) |
| `EventRegistrationReceived` | Events | Email (send confirmation), CRM (create/update contact) |
| `EventStartingSoon` | Events | Email (send reminder) |
| `AffiliateCommissionEarned` | Affiliates | Finance (record payable) |
| `TicketResolved` | CRM (Phase 3) | Email (send CSAT survey) |
| `CheckoutCompleted` | Ecommerce (Phase 4) | Email (post-purchase sequence) |
| `CartAbandoned` | Ecommerce (Phase 4) | Email (abandoned cart sequence) |

## Permissions Prefix

`marketing.content.*` · `marketing.email.*` · `marketing.forms.*`  
`marketing.social.*` · `marketing.seo.*` · `marketing.ads.*`  
`marketing.events.*` · `marketing.affiliates.*`

## Database Migration Range

`400000–449999`

## Note on CMS vs Marketing Site

The `marketing` panel is the **in-platform marketing tool** for FlowFlex customers — they build their own marketing assets here. This is separate from the FlowFlex **public website** (admin panel CMS, `14 - Marketing Site/`).

## Related

- [[CMS & Website Builder]]
- [[Email Marketing]]
- [[Forms & Lead Capture]]
- [[Social Media Management]]
- [[SEO & Analytics]]
- [[Ad Campaign Management]]
- [[Events & Webinars]]
- [[Affiliate & Partner Management]]
- [[Panel Map]]
- [[Build Order (Phases)]]
