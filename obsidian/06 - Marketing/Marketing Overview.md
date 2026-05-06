---
tags: [flowflex, domain/marketing, overview, phase/4]
domain: Marketing & Content
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-06
---

# Marketing Overview

The marketing and content creation domain. CMS, email campaigns, social, forms, SEO, ads, events, and affiliate management.

**Filament Panel:** `marketing`
**Domain Colour:** Pink `#DB2777` / Light: `#FCE7F3`
**Domain Icon:** `megaphone` (Heroicons)
**Phase:** 4 (core: CMS, Email Marketing, Forms & Lead Capture) + 5 (full suite)

## Modules in This Domain

| Module | Phase | Description |
|---|---|---|
| [[CMS & Website Builder]] | 4 | Block-based CMS, blog, SEO, media library |
| [[Email Marketing]] | 4 | Campaign builder, automation flows, A/B testing |
| [[Forms & Lead Capture]] | 4 | Drag-and-drop form builder, CRM auto-create |
| [[Social Media Management]] | 5 | Multi-channel publishing, content calendar |
| [[SEO & Analytics]] | 5 | Rank tracking, technical audit, GA4 |
| [[Ad Campaign Management]] | 5 | Google Ads, Meta Ads, ROAS dashboard |
| [[Events & Webinars]] | 5 | Registration, attendees, QR check-in |
| [[Affiliate & Partner Management]] | 5 | Affiliate portal, commissions, payouts |

## Key Events Consumed

| Event | From | What Marketing Does |
|---|---|---|
| `TicketResolved` | [[Customer Support & Helpdesk]] | Sends CSAT survey via email |
| `BurnoutSignalDetected` | (n/a — internal HR only) | — |
| (Churn signal) | [[Loyalty & Retention]] | Triggers win-back campaign |

## Related

- [[CMS & Website Builder]]
- [[Email Marketing]]
- [[Forms & Lead Capture]]
- [[Panel Map]]
