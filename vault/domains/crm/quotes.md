---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.quotes
status: planned
color: "#4ADE80"
---

# Quotes

> Quote creation from a deal, line items pre-filled from deal products, discounts, branded PDF generation, and e-signature integration.

**Panel:** `crm`
**Module key:** `crm.quotes`

## What It Does

Quotes allows sales reps to generate professional branded proposals directly from a deal record. Products linked to the deal are pre-populated as line items. The rep adds or adjusts items, applies line-level or quote-level discounts, and adds terms and conditions. The quote is rendered as a PDF using the company's branding (logo, colours from Company Settings) and delivered to the contact by email. The contact can accept the quote via an e-signature link. On acceptance, the quote status updates and can trigger contract creation or invoice generation.

## Features

### Core
- Quote creation: linked to a deal, pre-populated with deal products as line items
- Line items: description, quantity, unit price, discount percentage, line total, tax code
- Quote-level discount: additional percentage or fixed amount discount applied after line totals
- PDF generation: branded PDF with company logo, contact details, line item table, subtotal, tax, total, and terms section
- Email delivery: send quote PDF to deal contact with a tracking pixel (open notification)

### Advanced
- E-signature: contact receives a signing link — DocuSign or built-in signature widget — signed quote stored as a document
- Quote versions: each revision of a quote creates a new version — prior versions retained; only one version is `active` at a time
- Validity date: configurable quote validity period — expired quotes flagged; contact reminded before expiry
- Acceptance trigger: on quote acceptance, optionally auto-create a contract (Contracts module) or invoice (Finance Invoicing module)
- Quote templates: reusable line item templates for common product bundles — pre-fill entire quote in one click

### AI-Powered
- Price optimisation: AI analyses historical win rates at different price points for similar product combinations and suggests the discount level most likely to win the deal without leaving money on the table
- Terms flagging: AI reviews quote terms against the company's standard terms template and highlights deviations that require legal review before sending

## Data Model

```erDiagram
    crm_quotes {
        ulid id PK
        ulid company_id FK
        ulid deal_id FK
        ulid contact_id FK
        string number "unique"
        integer version
        string status
        date valid_until
        decimal subtotal
        decimal discount_amount
        decimal tax_total
        decimal total
        string currency
        text terms
        string pdf_path
        timestamp signed_at
        timestamps created_at/updated_at
    }

    crm_quote_lines {
        ulid id PK
        ulid quote_id FK
        string description
        decimal quantity
        decimal unit_price
        decimal discount_pct
        decimal tax_rate
        decimal line_total
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | draft / sent / accepted / declined / expired / void |
| `version` | Incremented on each revision |
| `signed_at` | Set when e-signature is completed |

## Permissions

- `crm.quotes.view`
- `crm.quotes.create`
- `crm.quotes.send`
- `crm.quotes.accept`
- `crm.quotes.manage-templates`

## Filament

- **Resource:** `QuoteResource`
- **Pages:** `ListQuotes`, `CreateQuote`, `EditQuote`, `ViewQuote` (with version history and signature status)
- **Custom pages:** None
- **Widgets:** `OpenQuotesWidget` — total value of sent/unsigned quotes on CRM dashboard
- **Nav group:** Pipeline (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| PandaDoc | Quote and proposal creation with e-signature |
| DocuSign | E-signature and quote delivery |
| Proposify | Sales proposal software |
| HubSpot Quotes | CRM-native quoting |

## Related

- [[deals]]
- [[contracts]]
- [[contacts]]
- [[revenue-intelligence]]
