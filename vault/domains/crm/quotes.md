---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.quotes
status: planned
color: "#4ADE80"
---

# Quotes

Generate quotes from deal line items, apply discounts, produce a PDF, and send for e-signature acceptance.

---

## Core Features

- Quote created from a deal — inherits contact, account, and products
- Line items: description, qty, unit price, discount %, line total
- Quote-level discount: additional % or fixed amount off subtotal
- Tax calculation from tax rates (if Tax module active)
- PDF generation: branded with company logo and color
- Status: `draft → sent → accepted | declined | expired`
- Quote validity period (default 30 days)
- Accept/decline tracking — acceptance converts quote to order/contract
- Versioning: create new version of an existing quote

---

## Data Model

| Table | Key Columns |
|---|---|
| `crm_quotes` | company_id, deal_id, contact_id, quote_number, status, valid_until, subtotal_cents, discount_cents, tax_cents, total_cents, sent_at, accepted_at |
| `crm_quote_lines` | quote_id, company_id, description, quantity, unit_price_cents, discount_percent, line_total_cents |

---

## Filament

**Nav group:** Pipeline

- `QuoteResource` — list, create, edit, send, view acceptance status
- PDF preview action on view page

---

## Related

- [[domains/crm/deals]]
- [[domains/crm/price-management]]
- [[domains/finance/invoicing]] — accepted quote creates invoice
