---
tags: [flowflex, domain/marketing, email-marketing, campaigns, phase/4]
domain: Marketing & Content
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-06
---

# Email Marketing

Campaign builder, automation flows, and deliverability tools. Replace Mailchimp.

**Who uses it:** Marketing team
**Filament Panel:** `marketing`
**Depends on:** Core, [[Contact & Company Management]] (contact lists)
**Phase:** 4
**Build complexity:** Very High — 3 resources, 3 pages, 8 tables

## Events Consumed

- `TicketResolved` (from [[Customer Support & Helpdesk]]) → sends CSAT survey
- (Churn signal from [[Loyalty & Retention]]) → triggers win-back campaign

## Features

- **Campaign builder** — compose one-off broadcast emails
- **Drag-and-drop email editor** — blocks: text, image, button, divider, columns
- **Automation flows** — welcome series, abandoned cart, re-engagement sequences
- **A/B testing** — split on subject line or email content
- **Open / click analytics** — per campaign stats
- **Unsubscribe and bounce management** — automatic suppression list handling
- **Deliverability tools** — DKIM, SPF, DMARC status checker

## Related

- [[Marketing Overview]]
- [[Contact & Company Management]]
- [[Customer Data Platform]]
- [[Forms & Lead Capture]]
