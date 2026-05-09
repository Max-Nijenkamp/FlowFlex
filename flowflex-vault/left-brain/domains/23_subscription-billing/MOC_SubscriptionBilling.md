---
type: moc
domain: Subscription Billing & RevOps
panel: subscriptions
phase: 3
color: "#10B981"
cssclasses: domain-subscriptions
last_updated: 2026-05-09
---

# Subscription Billing & RevOps — Map of Content

Recurring billing infrastructure for SaaS companies. Subscription lifecycle management, dunning, proration, usage-based billing, revenue recognition (IFRS 15/ASC 606), and MRR/ARR analytics. Replaces Chargebee, Recurly, Maxio, and Stripe Billing.

**Panel:** `subscriptions`  
**Phase:** 3  
**Migration Range:** `975000–979999`  
**Colour:** Emerald `#10B981` / Light: `#D1FAE5`  
**Icon:** `heroicon-o-arrow-path`

---

## Why This Domain Exists

FlowFlex's primary ICP includes SaaS founders and product companies. They have unique billing needs that standard invoicing cannot handle:
- Subscriptions that auto-renew on exact dates
- Proration when customers upgrade mid-cycle
- Usage-based charges on top of base subscription
- Dunning (automatic retry + recovery emails for failed payments)
- Revenue recognition that differs from cash collection (deferred revenue)
- Investor-grade MRR/ARR/churn metrics

Without this domain, SaaS companies using FlowFlex must also subscribe to Chargebee (€400/mo) or Recurly (€300/mo) just for billing.

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Subscription Lifecycle Management | 3 | planned | Plans, subscriptions, upgrades, downgrades, pauses, cancellations |
| Recurring Billing Engine | 3 | planned | Auto-charge on renewal date, proration, multi-currency |
| Dunning Management | 3 | planned | Failed payment retry schedules, recovery emails, involuntary churn prevention |
| Usage-Based Billing | 4 | planned | Metered charges, usage tracking, overage billing |
| Revenue Recognition (IFRS 15) | 4 | planned | Deferred revenue, recognition schedules, ASC 606 compliance |
| MRR / ARR Analytics | 3 | planned | MRR movements (new/expansion/contraction/churn), cohort analysis |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `SubscriptionCreated` | Lifecycle | Finance (revenue), CS (onboarding trigger), Notifications |
| `SubscriptionCancelled` | Lifecycle | CS (churn playbook), Finance (churn MRR), Analytics |
| `PaymentFailed` | Billing Engine | Dunning (trigger retry), Notifications (customer) |
| `DunningExhausted` | Dunning | CS (at-risk flag), Finance (involuntary churn) |
| `UsageThresholdReached` | Usage Billing | Notifications (customer + billing), Finance |

---

## Filament Panel Structure

**Navigation Groups:**
- `Plans` — Plan Catalogue, Pricing, Add-Ons
- `Subscriptions` — All Subscriptions, Active, Cancelled, Trials
- `Billing` — Upcoming Charges, Failed Payments, Dunning Queue
- `Usage` — Usage Records, Meter Definitions, Overage Billing
- `Revenue` — MRR Dashboard, Cohort Analysis, Recognition Schedule

---

## Permissions Prefix

`subscriptions.plans.*` · `subscriptions.billing.*` · `subscriptions.dunning.*`  
`subscriptions.usage.*` · `subscriptions.revenue.*`

---

## Competitors Displaced

Chargebee · Recurly · Maxio (SaaSOptics/Chargify) · Paddle · Stripe Billing · Zuora

---

## Related

- [[MOC_Domains]]
- [[MOC_Finance]] — recognized revenue → GL; deferred revenue → balance sheet
- [[MOC_CRM]] — subscription linked to CRM company/contact
- [[MOC_CustomerSuccess]] — subscription status feeds health score
- [[MOC_Ecommerce]] — ecommerce subscription products separate module
