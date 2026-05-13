---
type: module
domain: Finance & Accounting
panel: finance
phase: 3
status: complete
cssclasses: domain-finance
migration_range: 257500–257999
last_updated: 2026-05-12
---

# VAT & Tax Filing

Automate VAT return preparation across multiple jurisdictions. Calculate VAT from invoices and expenses, generate returns in the correct local format, and manage tax rates by country and product type.

---

## VAT Calculation

Every sales invoice and supplier invoice tagged with VAT rate at line level:
- Rate driven by: country of supply, product/service type, customer VAT status
- Standard / reduced / zero / exempt / reverse charge
- EU B2B: reverse charge applied; buyer self-accounts
- EU B2C: OSS (One Stop Shop) threshold rules

---

## Multi-Jurisdiction

Support for:
| Region | Mechanism |
|---|---|
| EU domestic | Standard VAT return per country |
| EU OSS | Single OSS return covering all EU B2C digital sales |
| UK | MTD VAT (Making Tax Digital) API submission |
| US | Sales tax by state (nexus-based) |
| Other | Manual return format per jurisdiction |

---

## VAT Return Preparation

At end of each VAT period:
1. System aggregates all transactions in period
2. Calculates: output tax (on sales), input tax (on purchases)
3. Net VAT due = output − input
4. Draft return generated for review
5. AP team reviews and adjusts (corrections, errors)
6. Submit: directly via API (MTD, OSS) or export XML/PDF

---

## Partial Exemption

For businesses with mixed taxable/exempt activities:
- Input VAT apportionment calculation
- Configurable partial exemption method

---

## Data Model

### `fin_tax_rates`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| country_code | char(2) | |
| rate_type | varchar(50) | standard/reduced/zero/exempt/reverse |
| rate | decimal(5,4) | e.g. 0.21 for 21% |
| valid_from | date | |
| valid_until | date | nullable |

### `fin_vat_returns`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| country_code | char(2) | |
| period_start | date | |
| period_end | date | |
| output_tax | decimal(14,2) | |
| input_tax | decimal(14,2) | |
| net_due | decimal(14,2) | |
| status | enum | draft/submitted/paid |
| submitted_at | timestamp | nullable |

---

## Migration

```
257500_create_fin_tax_rates_table
257501_create_fin_vat_returns_table
257502_create_fin_vat_return_lines_table
```

---

## Related

- [[MOC_Finance]]
- [[general-ledger-chart-of-accounts]]
- [[accounts-receivable-automation]]
- [[multi-entity-consolidation]]
