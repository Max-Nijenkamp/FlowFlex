---
domain: finance
module: tax-management
type: module
module-key: finance.tax
priority: v1
build-status: planned
status: wip
depends-on: [finance.ledger, core.billing, core.rbac]
soft-depends: [finance.invoicing, finance.ap, finance.expenses]
fires-events: []
consumes-events: []
patterns: [money, custom-pages]
tables: [fin_tax_rates, fin_tax_classes, fin_tax_periods]
permission-prefix: finance.tax
encrypted-fields: []
color: "#4ADE80"
updated: 2026-06-20
---

# Tax Management

Tax rate configuration, VAT/GST calculation on invoices and bills, and tax period reporting. EU VAT-focused for the primary market.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Purpose

A single tax engine for the Finance domain. `TaxCalculator` is the one sanctioned entry point for tax math across consuming modules (invoicing, AP, expenses) so rounding stays consistent. Configured rates and classes drive line-level tax; period summaries roll output vs input tax into a VAT return.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | VAT control accounts |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../invoicing/_module\|finance.invoicing]], [[../accounts-payable/_module\|finance.ap]], [[../expenses/_module\|finance.expenses]] | the consumers of configured rates (output/input tax) |

## Core Features

- Tax rate records: name, rate %, type (VAT/GST/sales tax), jurisdiction.
- Tax classes per product/service (standard, reduced, zero, exempt).
- Tax applied per invoice/bill line from configured rates (`TaxCalculator` — brick/money, line-level rounding consistent with invoicing).
- EU VAT specifics: reverse charge (intra-EU B2B — zero tax + ledger note), OSS reporting summary *(assumed: report only, no OSS filing integration v1)*.
- VAT number validation (VIES for EU — `Http` call, mocked in tests, failure-tolerant).
- Tax period summary: total output tax (collected) vs input tax (paid).
- VAT return preparation report.
- Multi-jurisdiction rates (for companies selling across borders).

## Permissions

`finance.tax.view` · `finance.tax.manage-rates` · `finance.tax.file-period`

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Tax math: 21% on €99.99 line exact via basis points + brick/money
- [ ] Reverse charge yields zero tax + flag carried to invoice line
- [ ] Period summary: output − input = net payable over fixtures
- [ ] Filed period locked against rate-affecting recomputation
- [ ] VIES failure doesn't block customer save
- [ ] Rate referenced by lines cannot be hard-deleted

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

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_tax_rates`, `fin_tax_classes`, `fin_tax_periods`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Reads by | rate reads (consumers own their own line tax amounts) | [[../invoicing/_module\|finance.invoicing]], [[../accounts-payable/_module\|finance.ap]], [[../expenses/_module\|finance.expenses]] |
| Reads | tax return reads output/input tax (read-only) | [[../invoicing/_module\|finance.invoicing]], [[../accounts-payable/_module\|finance.ap]] |

## Entity Notes

- [[architecture]] — services, tax math, money handling
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, period locking
- [[decisions]] — basis-points + report-only OSS deviations
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/tax-rates]], [[features/tax-report]]

## Related

- [[../invoicing/_module]]
- [[../accounts-payable/_module]]
- [[../financial-reporting/_module]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
