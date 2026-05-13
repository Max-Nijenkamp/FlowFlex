---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.assets
status: planned
color: "#4ADE80"
---

# Fixed Assets

> Fixed asset register, depreciation schedules, depreciation journal posting, and disposal tracking — the full lifecycle of company-owned assets.

**Panel:** `finance`
**Module key:** `finance.assets`

## What It Does

Fixed Assets manages the company's long-term physical and intangible assets — computers, furniture, vehicles, software licences, patents. Each asset is registered with its purchase cost, asset category, useful life, and depreciation method. The module auto-calculates and posts monthly depreciation journal entries to the General Ledger (debit Depreciation Expense, credit Accumulated Depreciation) based on the configured schedule. Disposals record the sale or write-off of an asset and post the gain/loss journal. The asset register provides the net book value of all assets at any point in time.

## Features

### Core
- Asset record: name, category, serial number, purchase date, purchase cost, supplier, useful life (years), residual value, depreciation method (straight-line, declining balance)
- Depreciation schedule: auto-calculated monthly depreciation amount per asset based on method and useful life
- Depreciation posting: monthly scheduled job posts depreciation journals to GL — debit Depreciation Expense, credit Accumulated Depreciation
- Net book value: purchase cost − accumulated depreciation — computed and shown on asset detail
- Asset register list: all assets with status, net book value, and depreciation progress bar

### Advanced
- Asset categories: user-defined categories (Computers & Electronics, Furniture, Vehicles, Intangibles) — each links to a GL asset account and depreciation expense account
- Bulk depreciation run: Finance triggers the monthly depreciation run manually or via scheduled job — previews all journal entries before posting
- Disposal workflow: record asset sold/scrapped — capture disposal proceeds, compute gain/loss, post disposal journal (credit Asset, debit Accumulated Depreciation + Bank/Gain or Loss)
- Asset revaluation: upward or downward revaluation with journal entry posted to Revaluation Reserve (equity)
- Fully depreciated assets: flagged in the register — Finance reviews and either continues to carry them (at residual value) or disposes

### AI-Powered
- Maintenance reminder: based on asset age and category, AI suggests when assets are likely to need maintenance or replacement — surfaced as reminders to the IT/operations team
- Impairment indicator: if an asset's market value (manually entered) drops significantly below net book value, AI flags it as a potential impairment test trigger

## Data Model

```erDiagram
    fixed_assets {
        ulid id PK
        ulid company_id FK
        string name
        string category
        string serial_number
        date purchase_date
        decimal purchase_cost
        decimal residual_value
        integer useful_life_years
        string depreciation_method
        decimal accumulated_depreciation
        string status
        date disposal_date
        decimal disposal_proceeds
        ulid asset_gl_account_id FK
        ulid depreciation_gl_account_id FK
        timestamps created_at/updated_at
    }

    asset_depreciation_entries {
        ulid id PK
        ulid asset_id FK
        ulid company_id FK
        date period_date
        decimal amount
        ulid journal_entry_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `depreciation_method` | straight_line / declining_balance |
| `status` | active / fully_depreciated / disposed |
| `asset_depreciation_entries` | One row per month per asset per posted depreciation |

## Permissions

- `finance.assets.view`
- `finance.assets.create`
- `finance.assets.post-depreciation`
- `finance.assets.dispose`
- `finance.assets.revalue`

## Filament

- **Resource:** `FixedAssetResource`
- **Pages:** `ListFixedAssets`, `CreateFixedAsset`, `ViewFixedAsset` (with depreciation schedule table)
- **Custom pages:** `DepreciationRunPage` — preview and post the monthly depreciation batch
- **Widgets:** `AssetNetBookValueWidget` — total net book value of all active assets on finance dashboard
- **Nav group:** Ledger (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero Fixed Assets | Asset register and depreciation |
| QuickBooks Fixed Assets | Fixed asset tracking |
| Sage Fixed Assets | Asset lifecycle management |
| Asset Panda | Fixed asset management |

## Related

- [[general-ledger]]
- [[financial-reporting]]
- [[budgets]]
