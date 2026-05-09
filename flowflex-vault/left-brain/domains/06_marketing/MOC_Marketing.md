---
type: moc
domain: Marketing & Content
panel: marketing
cssclasses: domain-marketing
phase: 5
color: "#DB2777"
last_updated: 2026-05-08
---

# Marketing & Content — Map of Content

CMS, email campaigns, forms, social, SEO, ads, events, affiliate management, AI content, SMS/WhatsApp, push notifications, and influencer management.

**Panel:** `marketing`  
**Phase:** 5  
**Migration Range:** `400000–449999`  
**Colour:** Pink `#DB2777` / Light: `#FCE7F3`  
**Icon:** `heroicon-o-megaphone`

> Note: This is the **in-platform marketing tool** FlowFlex customers use to run their own marketing. Separate from the FlowFlex public website (see [[MOC_Frontend]]).

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| CMS & Website Builder | 5 | planned | Content pages, blocks, media, redirects, SEO |
| Email Marketing | 5 | planned | Campaign builder, sequences, A/B testing |
| Forms & Lead Capture | 5 | planned | Form builder, conditional logic, CRM auto-create |
| [[social-media-management\|Social Media Management]] | 5 | planned | Multi-platform publishing, content calendar, analytics |
| SEO & Analytics | 5 | planned | SEO audits, keyword rankings, GA4 snapshots |
| Ad Campaign Management | 5 | planned | Google/Meta/LinkedIn/TikTok ad tracking |
| Events & Webinars | 5 | planned | Event management, registrations, QR check-in |
| [[affiliate-program\|Affiliate Program]] | 5 | planned | Affiliates, tracking links, commission lifecycle, punchout |
| [[marketing-attribution\|Marketing Attribution]] | 5 | planned | Multi-touch attribution models, touchpoint capture, ROI per channel |
| AI Content Studio | 5 | planned | AI blog/email/social/ad copy, brand voice |
| SMS & WhatsApp Marketing | 5 | planned | Broadcast + flow campaigns, GDPR opt-in |
| Push Notifications | 5 | planned | Web push + mobile push, send-time optimisation |
| Influencer & UGC Management | 5 | planned | Influencer campaigns, UGC rights, ROI tracking |
| [[referral-program\|Referral Program Management]] | 5 | planned | Customer-refers-customer programs, reward automation |
| [[review-reputation-management\|Review & Reputation Management]] | 5 | planned | Monitor/respond to Google, Trustpilot, G2 reviews |
| [[digital-asset-management\|Digital Asset Management (DAM)]] | 5 | planned | Brand asset library, version control, licensing, brand portal |
| [[landing-page-builder\|Landing Page Builder]] | 5 | planned | Conversion-optimised campaign pages, A/B testing, no navigation |
| [[utm-link-management\|UTM Builder & Link Management]] | 5 | planned | UTM links, branded short links, click analytics, campaign organisation |
| [[contact-behavioral-scoring\|Contact Behavioral Scoring]] | 4 | planned | Real-time lead scoring, intent signals, MQL thresholds, CRM sync |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `FormSubmissionReceived` | Forms | CRM (create contact), Email (trigger sequence) |
| `EmailCampaignSent` | Email Marketing | Analytics (track stats) |
| `EventRegistrationReceived` | Events | Email (confirmation), CRM (create/update contact) |
| `AffiliateCommissionEarned` | Affiliates | Finance (record payable) |
| `SocialPostPublished` | Social | Analytics (track engagement) |
| `TicketResolved` | CRM (consumed) | Email (CSAT survey) |
| `CheckoutCompleted` | Ecommerce (consumed) | Email (post-purchase sequence) |
| `CartAbandoned` | Ecommerce (consumed) | Email (abandoned cart sequence) |

---

## Navigation Groups (Filament Panel)

- `Content` — Content Pages, Media Library, Redirects
- `Campaigns` — Email Campaigns, Email Sequences, Social Posts, Ad Campaigns
- `Capture` — Forms, Form Submissions
- `Events` — Events, Registrations
- `Partners` — Affiliates, Referrals, Payouts

---

## Competitors Displaced

Mailchimp · HubSpot Marketing · Hootsuite · Buffer · Typeform · Ahrefs · Tapfiliate · Influencer.co

---

## Related

- [[MOC_Domains]]
- [[MOC_Frontend]] — public website is NOT this panel
- [[MOC_CRM]] — form submissions → contacts
- [[MOC_Finance]] — affiliate commissions → payable
