---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.tax
status: planned
priority: v1
depends-on: [finance.ledger, core.billing, core.rbac]
soft-depends: [finance.invoicing, finance.ap, finance.expenses]
fires-events: []
consumes-events: []
patterns: [money, custom-pages]
tables: [fin_tax_rates, fin_tax_classes, fin_tax_periods]
permission-prefix: finance.tax
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Tax Management

Tax rate configuration, VAT/GST calculation on invoices and bills, and tax period reporting. EU VAT-focused for the primary market.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/general-ledger\|finance.ledger]] | VAT control accounts |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/finance/invoicing\|finance.invoicing]], [[domains/finance/accounts-payable\|finance.ap]], [[domains/finance/expenses\|finance.expenses]] | the consumers of configured rates (output/input tax) |

---

## Core Features

- Tax rate records: name, rate %, type (VAT/GST/sales tax), jurisdiction
- Tax classes per product/service (standard, reduced, zero, exempt)
- Tax applied per invoice/bill line from configured rates (`TaxCalculator` — brick/money, line-level rounding consistent with invoicing)
- EU VAT specifics: reverse charge (intra-EU B2B — zero tax + ledger note), OSS reporting summary *(assumed: report only, no OSS filing integration v1)*
- VAT number validation (VIES for EU — `Http` call, mocked in tests, failure-tolerant)
- Tax period summary: total output tax (collected) vs input tax (paid)
- VAT return preparation report
- Multi-jurisdiction rates (for companies selling across borders)

---

## Data Model

### fin_tax_rates

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | e.g. "NL High 21%" |
| rate_basis_points | int | 2100 = 21% — integer, no float *(was rate_percent in v1 spec)* |
| type | string | vat / gst / sales-tax |
| jurisdiction | string | ISO country |
| is_reverse_charge | boolean | default false |
| is_active | boolean | default true |
| deleted_at | timestamp nullable | rates referenced by lines never hard-deleted |

### fin_tax_classes

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | standard / reduced / zero / exempt |
| default_rate_id | ulid FK fin_tax_rates | |

### fin_tax_periods

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| period | string | `YYYY-Qn` or `YYYY-MM`, unique per company |
| output_tax_cents / input_tax_cents / net_payable_cents | bigint | computed snapshot |
| status | string default `open` | open / filed |

---

## DTOs

### CreateTaxRateData — name, rate_basis_points (0–10000), type (in set), jurisdiction (ISO country), is_reverse_charge
### TaxReturnData (output) — period, output_tax_cents, input_tax_cents, net_payable_cents, breakdown per rate

## Services & Actions

- `TaxCalculator::forLine(int $amountCents, TaxRate $rate): Money` — single tax math entry point for all consuming modules
- `TaxService::periodSummary(string $period): TaxReturnData` — sums invoice output tax + bill/expense input tax
- `TaxService::filePeriod(string $period): void` — snapshot + status filed (locked)
- `ValidateVatNumberAction::run(string $vatNumber): bool` — VIES; network failure = "unverified", never blocks save *(assumed)*

---

## Filament

**Nav group:** Reporting

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `TaxRateResource` | #1 CRUD resource | rates + classes management |
| `TaxReturnPage` | #9 report custom page | period summary, VAT return prep, file action |
| VAT-validate action | table/form action | on customer/supplier records |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('finance.tax.view-any') && BillingService::hasModule('finance.tax')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`finance.tax.view` · `finance.tax.manage-rates` · `finance.tax.file-period`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Tax math: 21% on €99.99 line exact via basis points + brick/money
- [ ] Reverse charge yields zero tax + flag carried to invoice line
- [ ] Period summary: output − input = net payable over fixtures
- [ ] Filed period locked against rate-affecting recomputation
- [ ] VIES failure doesn't block customer save
- [ ] Rate referenced by lines cannot be hard-deleted

---

## Build Manifest

```
database/migrations/xxxx_create_fin_tax_rates_table.php
database/migrations/xxxx_create_fin_tax_classes_table.php
database/migrations/xxxx_create_fin_tax_periods_table.php
app/Models/Finance/{TaxRate,TaxClass,TaxPeriod}.php
app/Data/Finance/{CreateTaxRateData,TaxReturnData}.php
app/Support/Finance/TaxCalculator.php
app/Services/Finance/TaxService.php
app/Actions/Finance/ValidateVatNumberAction.php
app/Filament/Finance/Resources/TaxRateResource.php
app/Filament/Finance/Pages/TaxReturnPage.php
database/factories/Finance/TaxRateFactory.php
tests/Feature/Finance/{TaxCalculationTest,TaxReturnTest,ViesValidationTest}.php
```

---

## Related

- [[domains/finance/invoicing]]
- [[domains/finance/accounts-payable]]
- [[build/decisions/decision-2026-06-01-currency-precision]]
