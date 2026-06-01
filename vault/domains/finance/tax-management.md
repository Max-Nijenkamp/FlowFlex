---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.tax
status: planned
color: "#4ADE80"
---

# Tax Management

Tax rate configuration, VAT/GST calculation on invoices and bills, and tax period reporting. EU VAT-focused for the primary market.

## Core Features

- Tax rate records: name, rate %, type (VAT/GST/sales tax), jurisdiction
- Tax classes per product/service (standard, reduced, zero, exempt)
- Tax applied per invoice/bill line from configured rates
- EU VAT specifics: reverse charge (intra-EU B2B), OSS reporting
- VAT number validation (VIES for EU)
- Tax period summary: total output tax (collected) vs input tax (paid)
- VAT return preparation report
- Multi-jurisdiction rates (for companies selling across borders)

## Data Model

| Table | Key Columns |
|---|---|
| `fin_tax_rates` | company_id, name, rate_percent, type, jurisdiction, is_reverse_charge |
| `fin_tax_classes` | company_id, name, default_rate_id |
| `fin_tax_periods` | company_id, period, output_tax_cents, input_tax_cents, net_payable_cents, status |

## Filament

**Nav group:** Reporting

- `TaxRateResource` — manage rates and classes
- `TaxReturnPage` (custom page) — period summary, VAT return prep
- VAT number validation action on customer/supplier

## Cross-Domain

- Tax rates applied in Invoicing, Expenses, AP, E-commerce, CRM Quotes
- Uses `brick/money` for precise tax calculation

## Related

- [[domains/finance/invoicing]]
- [[domains/finance/accounts-payable]]
- `brick/money`
