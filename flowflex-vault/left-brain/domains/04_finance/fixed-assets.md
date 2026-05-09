---
type: module
domain: Finance & Accounting
panel: finance
phase: 3
status: planned
cssclasses: domain-finance
migration_range: 257000–257499
last_updated: 2026-05-09
---

# Fixed Assets

Track owned and leased assets throughout their full lifecycle — acquisition, depreciation, revaluation, and disposal. Ensures accurate balance sheet values and automates depreciation journals.

---

## Asset Register

Central register of all fixed assets:
- Asset ID, description, category, location
- Purchase date, purchase cost, supplier
- Useful life (years), residual value
- Depreciation method: straight-line, declining balance, units of production
- GL account (cost account + accumulated depreciation account)
- Responsible department / cost centre
- Serial number, warranty expiry

---

## Depreciation

Auto-calculated monthly depreciation per asset:
```
Monthly depreciation = (Cost − Residual value) / Useful life in months
```

Monthly journal auto-generated:
```
DR Depreciation expense    500.00
  CR Accumulated depreciation    500.00
```

Depreciation run: triggered monthly, pushed as draft to GL for review.

---

## Depreciation Methods

| Method | Best For |
|---|---|
| Straight-line | Buildings, furniture, standard equipment |
| Declining balance | IT equipment, vehicles |
| Units of production | Plant machinery tied to output |
| IFRS 16 right-of-use | Leased assets |

---

## Asset Lifecycle

```
Capitalised → In Use → Partially Depreciated → Fully Depreciated
           → Impaired (write-down)
           → Disposed (sold, scrapped, donated)
```

On disposal: system calculates profit/loss on disposal and generates disposal journal.

---

## Asset Tracking

Physical verification:
- QR code labels generated per asset
- Annual audit: scan assets to confirm location/existence
- Lost/stolen: flag → write-off at net book value

---

## Data Model

### `fin_assets`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| asset_number | varchar(50) | unique per tenant |
| name | varchar(300) | |
| category | varchar(100) | |
| status | enum | in_use/disposed/impaired |
| cost | decimal(14,2) | |
| residual_value | decimal(14,2) | |
| acquisition_date | date | |
| useful_life_months | int | |
| depreciation_method | enum | straight_line/declining_balance/units |
| accumulated_depreciation | decimal(14,2) | |
| net_book_value | decimal(14,2) | |

### `fin_asset_depreciation_runs`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| period | date | month |
| total_depreciation | decimal(14,2) | |
| gl_journal_id | ulid | nullable FK |
| run_at | timestamp | |

---

## Migration

```
257000_create_fin_assets_table
257001_create_fin_asset_depreciation_runs_table
257002_create_fin_asset_disposals_table
```

---

## Related

- [[MOC_Finance]]
- [[general-ledger-chart-of-accounts]]
- [[MOC_Procurement]] — asset acquisition via PO
