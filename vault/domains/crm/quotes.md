---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.quotes
status: planned
priority: v1-core
depends-on: [crm.deals, core.billing, core.rbac, foundation.queues]
soft-depends: [crm.pricing, finance.tax, finance.invoicing]
fires-events: []
consumes-events: []
patterns: [states, money, pdf, email]
tables: [crm_quotes, crm_quote_lines]
permission-prefix: crm.quotes
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Quotes

Generate quotes from deal line items, apply discounts, produce a PDF, and send for acceptance.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/deals\|crm.deals]] | quotes created from deals, inherit contact/account/products |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, PDF/mail jobs |
| Soft | crm.pricing | catalog products + CPQ; free-text lines without it |
| Soft | [[domains/finance/tax-management\|finance.tax]] | line tax; default rate otherwise |
| Soft | [[domains/finance/invoicing\|finance.invoicing]] | acceptance → deal won → invoice stub via existing DealWon flow |

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

## Data Model

### crm_quotes

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| deal_id | ulid | not null FK | |
| contact_id | ulid | nullable FK | recipient |
| quote_number | string | unique `(company_id, quote_number)` | Q-2026-001, assigned at send |
| version | int | default 1 | unique `(deal_id, quote_number base, version)` *(assumed)* |
| status | string | default `draft` | state machine |
| valid_until | date | default issue + 30d | |
| subtotal_cents / discount_cents / tax_cents / total_cents | bigint | computed | |
| currency | string(3) | | |
| accept_token | uuid | unique | public accept link |
| sent_at / accepted_at / declined_at | timestamp nullable | | |
| pdf_path | string nullable | | |
| deleted_at | timestamp nullable | | |

### crm_quote_lines

| Column | Type | Notes |
|---|---|---|
| id, quote_id FK, company_id | ulid | |
| product_id | ulid nullable | crm.pricing |
| description | string | |
| quantity | decimal(10,2) | min 0.01 |
| unit_price_cents | bigint | |
| discount_percent | decimal(5,2) | 0–100 |
| tax_rate_id | ulid nullable | |
| line_total_cents | bigint | computed |

---

## State Machine

Column: `crm_quotes.status` — `QuoteState`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `sent` | `crm.quotes.send` | number assigned, PDF generated, mail with accept link |
| `sent` | `accepted` | public accept (token) or rep | deal products synced from quote lines; rep notified |
| `sent` | `declined` | public decline or rep | reason captured *(assumed optional)* |
| `sent` | `expired` | scheduled command past `valid_until` | |
| `draft`/`sent` | superseded by new version | `crm.quotes.create` (version action) | old version locked read-only |

Audited.

---

## DTOs

### CreateQuoteData — deal_id (required), lines[{product_id?, description, quantity, unit_price_cents, discount_percent, tax_rate_id?}] min:1 (prefilled from deal products), quote_discount {type: percent|fixed, value}, valid_until (after:today)
### QuoteData (output) — id, quote_number, version, deal_name, contact_name, status, totals (all cents + formatted), valid_until, lines[]

## Services & Actions

Interface→Service: `QuoteServiceInterface` → `QuoteService`.

- `createFromDeal(CreateQuoteData $data): QuoteData` — totals via brick/money (line rounding consistent with invoicing)
- `send(string $quoteId): QuoteData`
- `accept(string $token): QuoteData` — public path; syncs deal products; notifies owner
- `decline(string $token, ?string $reason): QuoteData`
- `newVersion(string $quoteId): QuoteData`

---

## Filament

**Nav group:** Pipeline

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `QuoteResource` | #1 CRUD resource | line repeater, send action, version action |
| Quote view page | #2 detail | PDF preview, acceptance status |

Public accept page: Vue + Inertia `/quotes/{token}` — ui-strategy row #16, rate-limited.

---

## Permissions

`crm.quotes.view-any` · `crm.quotes.view` · `crm.quotes.create` · `crm.quotes.update` · `crm.quotes.send` · `crm.quotes.delete`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ExpireQuotesCommand` | default | daily | WHERE `status=sent AND valid_until < today` |
| `GenerateQuotePdfJob` | exports | on send | overwrites |
| `QuoteMail` | notifications | on send | — |

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

## Related

- [[domains/crm/deals]]
- [[domains/crm/price-management]]
- [[domains/finance/invoicing]]
- [[domains/finance/tax-management]]
