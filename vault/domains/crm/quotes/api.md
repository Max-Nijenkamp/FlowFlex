---
domain: crm
module: quotes
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Quotes — API & DTOs

See also [[data-model|quotes.data-model]], [[security|quotes.security]], [[../../../architecture/ui-strategy]].

---

## DTOs

### CreateQuoteData (input)

| Field | Type | Validation |
|---|---|---|
| deal_id | string | required, ulid in company |
| lines | array | min:1 |
| lines[].product_id | ?string | ulid, nullable (crm.pricing) |
| lines[].description | string | required |
| lines[].quantity | decimal | min:0.01 |
| lines[].unit_price_cents | int | min:0 |
| lines[].discount_percent | decimal | 0–100 |
| lines[].tax_rate_id | ?string | ulid, nullable (finance.tax) |
| quote_discount | object | type: percent\|fixed, value |
| valid_until | date | after:today |

Lines prefilled from deal products when creating from a deal.

### QuoteData (output)

| Field | Type | Notes |
|---|---|---|
| id | string | ulid |
| quote_number | string | assigned at send |
| version | int | |
| deal_name | string | |
| contact_name | string | |
| status | string | draft\|sent\|accepted\|declined\|expired |
| subtotal_cents / discount_cents / tax_cents / total_cents | int | all amounts |
| subtotal / discount / tax / total | string | formatted (brick/money) |
| valid_until | date | |
| lines | array | each line with amounts |

---

## Public Endpoints

### GET `/quotes/{token}` — Public Quote Acceptance Page

- Guard: guest (no app session)
- Rate-limited
- Returns: Vue+Inertia page (ui-strategy row #16) with quote details and accept/decline buttons
- Token validated against `crm_quotes.accept_token` (unique UUID)

### POST `/quotes/{token}/accept` — Accept Quote

- Guard: guest
- Rate-limited
- Calls: `QuoteService::accept(string $token): QuoteData`
- Side effects: deal products synced; rep notified; status → accepted

### POST `/quotes/{token}/decline` — Decline Quote

- Guard: guest
- Rate-limited
- Body: `{ reason?: string }`
- Calls: `QuoteService::decline(string $token, ?string $reason): QuoteData`
- Side effects: status → declined

**Security:** See [[security|quotes.security]] — public/portal guard HIGH priority note.
