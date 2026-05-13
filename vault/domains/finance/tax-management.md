---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.tax
status: planned
color: "#4ADE80"
---

# Tax Management

> Tax rates, tax codes, VAT/GST calculation on invoices and bills, tax liability tracking, and tax return preparation reports.

**Panel:** `finance`
**Module key:** `finance.tax`

## What It Does

Tax Management provides the tax configuration layer that Invoicing and Accounts Payable use to calculate and record tax amounts. Finance configures tax rates (standard VAT, reduced VAT, zero-rated, exempt) per jurisdiction. When an invoice or supplier bill is created, the applicable tax rate is applied per line item. The tax module tracks total VAT collected from customers and total VAT paid to suppliers — the difference is the VAT liability to the tax authority. A VAT return report produces the figures needed to complete a VAT return without leaving FlowFlex.

## Features

### Core
- Tax rates: name, rate percentage, type (standard / reduced / zero / exempt / reverse_charge), jurisdiction (country)
- Tax codes: named codes applied to GL accounts and invoice line items (e.g. `NL_VAT_21`, `UK_VAT_20`, `EXEMPT`)
- Invoice tax calculation: when a line item is added to an invoice or bill, the configured tax code auto-applies the rate and computes the tax amount
- GL posting: tax amounts posted to a dedicated VAT Collected (liability) or VAT Paid (asset) GL account — separate from revenue and expense lines
- Tax liability summary: net VAT position (collected − paid) per tax period

### Advanced
- Multi-jurisdiction: support multiple tax rates simultaneously — companies operating in multiple countries or with mixed product types
- Reverse charge: for cross-border B2B transactions within the EU — VAT liability shifts to buyer; both output and input VAT posted simultaneously
- VAT return report: period-selectable report computing Box 1–9 values (NL) or equivalent for UK/DE — export as PDF or XML
- Tax period lock: when a VAT return is filed, lock the period to prevent amendment of VAT figures
- EC sales list: list of all zero-rated intra-EU sales for reporting to the tax authority (VIES)

### AI-Powered
- Tax code suggestions: when a new invoice line item description is entered, AI suggests the most likely applicable tax code based on the product/service description
- Compliance alerts: AI monitors tax rate changes announced by tax authorities and alerts Finance when a jurisdiction rate is scheduled to change — prompts to update the rate before the effective date

## Data Model

```erDiagram
    tax_rates {
        ulid id PK
        ulid company_id FK
        string name
        string code "unique per company"
        decimal rate
        string type
        string jurisdiction
        boolean is_active
        timestamps created_at/updated_at
    }

    tax_transactions {
        ulid id PK
        ulid company_id FK
        ulid tax_rate_id FK
        string document_type
        ulid document_id FK
        decimal taxable_amount
        decimal tax_amount
        string direction
        date transaction_date
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` | standard / reduced / zero / exempt / reverse_charge |
| `direction` | collected (from customer) / paid (to supplier) |
| `document_type` | invoice / supplier_bill |

## Permissions

- `finance.tax.view-rates`
- `finance.tax.manage-rates`
- `finance.tax.view-liability`
- `finance.tax.generate-return`
- `finance.tax.lock-period`

## Filament

- **Resource:** `TaxRateResource`
- **Pages:** `ListTaxRates`
- **Custom pages:** `VatReturnPage` — period selector with computed return boxes and export
- **Widgets:** `TaxLiabilityWidget` — current VAT liability balance on finance dashboard
- **Nav group:** Reporting (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero Tax | VAT rates and tax reporting |
| QuickBooks Tax Centre | Sales tax management |
| Taxamo | European VAT compliance |
| Avalara | Tax calculation and compliance |

## Related

- [[general-ledger]]
- [[invoicing]]
- [[accounts-payable]]
- [[financial-reporting]]
- [[multi-currency]]
