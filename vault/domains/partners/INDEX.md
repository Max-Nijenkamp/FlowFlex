---
type: domain-index
domain: Partner & Channel
panel: partners
panel-path: /partners
panel-color: Green
color: "#4ADE80"
---

# Partner & Channel

Partner & Channel is the indirect sales and ecosystem domain of FlowFlex. It manages relationships with resellers, affiliates, referral partners, and integration partners — covering onboarding, deal registration, commission calculation and payout, co-marketing asset sharing, MDF management, and affiliate conversion tracking. The Partners panel lives at `/partners`.

**Important distinction:** This domain manages indirect channel relationships (partners who sell on behalf of the company or refer customers). It is distinct from the **CRM & Sales** domain, which manages direct customer relationships. Partners may be linked to CRM contacts, but they are managed through a dedicated partner data model with its own auth guard and partner-facing portal.

## Navigation Groups

- **Partners** — Partner list, Applications, Onboarding
- **Deals** — Deal Registrations
- **Finance** — Commissions, Payouts
- **Resources** — Assets, MDF, Affiliates
- **Settings** — Tiers, Commission Rules, Portal Config

## Modules

| Module | File | Module Key | Description |
|---|---|---|---|
| Partner Portal | [[partner-portal]] | `partners.portal` | Partner-facing microsite at /partner-portal with deal pipeline, commissions, resources, and tier display |
| Deal Registration | [[deal-registration]] | `partners.deal-registration` | Partners submit deals for approval; 90-day protection on approved deals; sync to CRM as opportunities |
| Partner Commissions | [[partner-commissions]] | `partners.commissions` | Commission rules by type and tier, calculation on deal close, approval workflow, payout scheduling |
| Partner Onboarding | [[partner-onboarding]] | `partners.onboarding` | Application form, review, onboarding checklist, e-signature, training tracks, certification |
| Co-Marketing | [[co-marketing]] | `partners.co-marketing` | Asset library, MDF budget allocation, fund requests, approval, proof of performance |
| Affiliate Management | [[affiliate-management]] | `partners.affiliates` | Unique referral links, conversion tracking, analytics, leaderboard, fraud detection |

## Primary Displaces

PartnerStack, Kiflo PRM, Impartner, Channelscaler, Impact.com, Tapfiliate

## Related

- [[domains/crm/INDEX]] — CRM & Sales (approved deals sync to CRM as opportunities)
- [[domains/lms/INDEX]] — Learning & Dev (partner training tracks and certifications)
- [[domains/finance/INDEX]] — Finance & Accounting (commission payouts as financial transactions)
- [[architecture/filament-patterns]]
- [[architecture/module-system]]
