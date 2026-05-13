---
type: domain-index
domain: Subscription Billing & RevOps
panel: billing
panel-path: /billing
panel-color: Violet
color: "#4ADE80"
---

# Subscription Billing & RevOps

FlowFlex's Billing panel manages the full subscription revenue lifecycle — plan catalog, automated invoicing via Stripe, failed payment dunning, ASC 606 / IFRS 15 revenue recognition, and MRR analytics — replacing Chargebee and Zuora for most SaaS use cases.

**Panel:** `billing` — `/billing`
**Filament Color:** Violet

---

## Modules

| Module | Key | Nav Group | Description |
|---|---|---|---|
| [[subscription-plans]] | billing.plans | Subscriptions | Subscription plan catalog: pricing tiers, features, trial periods |
| [[invoicing]] | billing.invoicing | Invoicing | Automated invoice generation for subscriptions via Stripe |
| [[dunning]] | billing.dunning | Invoicing | Failed payment recovery: retry schedules, email sequences, suspension |
| [[revenue-recognition]] | billing.recognition | Revenue | ASC 606 / IFRS 15 revenue recognition: deferred revenue, schedules |
| [[mrr-analytics]] | billing.mrr | Revenue | MRR dashboard: new, expansion, contraction, churn, NRR |

---

## Nav Groups

- **Subscriptions** — Subscription Plans
- **Invoicing** — Invoicing, Dunning
- **Revenue** — Revenue Recognition, MRR Analytics
- **Settings** — Billing configuration, Stripe integration, tax settings

---

## Displaces

| Competitor | Displaced By |
|---|---|
| Chargebee | Subscription billing, invoicing, and MRR analytics in one platform |
| Zuora | Revenue recognition and subscription management without a separate system |
| Paddle | Billing and dunning integrated with FlowFlex CS and CRM |

---

## Related

- [[crm/INDEX]] — customer accounts linked to subscriptions
- [[customer-success/INDEX]] — billing health signals feed CS health scores
- [[finance/INDEX]] — recognised revenue and deferred revenue posted to finance ledger
- [[plg/trial-management]] — trial expiry triggers subscription activation
