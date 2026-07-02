---
domain: crm
module: quotes
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Quotes

Generate quotes from deal line items, apply discounts, produce a PDF, and send for acceptance.

> All work here is **planned** — the CRM code was stripped back to an app/admin shell. See [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../deals/_module\|crm.deals]] | quotes created from deals, inherit contact/account/products |
| Hard | core.billing + core.rbac + foundation.queues | gating, permissions, PDF/mail jobs |
| Soft | crm.pricing | catalog products + CPQ; free-text lines without it |
| Soft | finance.tax | line tax; default rate otherwise |
| Soft | finance.invoicing | acceptance → deal won → invoice stub via existing DealWon flow |

---

## Core Features

- Quote created from a deal — inherits contact, account, and products
- Line items: description, qty, unit price, discount %, line total
- Quote-level discount: additional % or fixed amount off subtotal
- Tax calculation from tax rates (if Tax module active)
- PDF generation: branded with company logo and color
- Status: `draft → sent → accepted | declined | expired`
- Quote validity period (default 30 days)
- Accept/decline tracking — acceptance pre-fills deal products and prompts rep to close deal as won (invoice then flows from `DealWon`; no separate quote event *(assumed)*)
- Public accept/decline page via signed link *(assumed: tokenised Vue page, ui-strategy row #16)*
- Versioning: create new version of an existing quote (old version locked)

---

## Build Manifest

```
database/migrations/xxxx_create_crm_quotes_table.php
database/migrations/xxxx_create_crm_quote_lines_table.php
app/Models/CRM/{Quote,QuoteLine}.php
app/States/CRM/Quote/{QuoteState,Draft,Sent,Accepted,Declined,Expired}.php
app/Data/CRM/{CreateQuoteData,QuoteData}.php
app/Contracts/CRM/QuoteServiceInterface.php
app/Services/CRM/QuoteService.php
app/Jobs/CRM/GenerateQuotePdfJob.php
app/Mail/CRM/QuoteMail.php
app/Console/Commands/CRM/ExpireQuotesCommand.php
app/Http/Controllers/PublicQuoteController.php + resources/js/Pages/Quotes/Accept.vue
app/Filament/CRM/Resources/QuoteResource.php
database/factories/CRM/{QuoteFactory,QuoteLineFactory}.php
tests/Feature/CRM/{QuoteLifecycleTest,QuoteAcceptTest,QuoteTotalsTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Totals: line discounts + quote discount + tax exact (brick/money fixture)
- [ ] Accept via token syncs deal products + notifies owner; token single-quote scoped
- [ ] Expired token/quote cannot be accepted
- [ ] New version locks old; only one open version per deal *(assumed)*
- [ ] Expire command only touches sent quotes past validity
- [ ] Public page rate-limited, no auth leak

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | `QuoteAccepted` | crm.deals (within CRM) | Consumed by deals to pre-fill products; rep then closes the deal won. Fired from the public-acceptance route and the internal accept action. |
| Fires | `QuoteSent` | crm.quotes (self) | Enqueues `GenerateQuotePdfJob`. |
| Reads | read query | crm.deals | Deal → contact/account/products to seed a quote. Read-only. |
| Reads | read API | crm.pricing | Line-item unit price via `PricingService::resolve()`. Read-only. |
| Reads | read query | crm.contacts | Contact details on the quote / PDF. Read-only. |
| Consumes | — | — | No cross-domain events consumed. Invoice flows from the existing `DealWon` path after the rep closes the deal; no separate quote→invoice event *(assumed)*. |

**Data ownership:** `crm.quotes` writes only `crm_quotes` and `crm_quote_lines` (plus versions modelled on those tables); all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../deals/_module|crm.deals]]
- [[../pipeline/_module|crm.pipeline]]
- [[architecture|quotes.architecture]]
- [[data-model|quotes.data-model]]
- [[api|quotes.api]]
- [[security|quotes.security]]
- [[features/pdf-generation|pdf-generation feature]]
- [[features/public-acceptance|public-acceptance feature]]
- [[features/versioning|versioning feature]]
- [[../../../architecture/event-bus]]
- [[../../../architecture/ui-strategy]]
- [[../../../architecture/filament-patterns]]
- [[../../../infrastructure/mail]]
- [[../../../glossary]]
