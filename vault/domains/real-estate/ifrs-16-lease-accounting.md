---
type: module
domain: Real Estate & Property
panel: realestate
module-key: realestate.ifrs16
status: planned
color: "#4ADE80"
---

# IFRS 16 Lease Accounting

> IFRS 16 / ASC 842 lease accounting — right-of-use asset calculation, lease liability amortisation schedule, and journal entry generation.

**Panel:** `realestate`
**Module key:** `realestate.ifrs16`

---

## What It Does

IFRS 16 Lease Accounting automates the accounting treatment of lease obligations under IFRS 16 (or ASC 842 for US GAAP entities). For each lease designated as an IFRS 16 lease, the module calculates the initial right-of-use (ROU) asset value and lease liability using the present value of future lease payments discounted at the incremental borrowing rate. It then produces a full amortisation schedule, generates the accounting journal entries for each period (interest expense, depreciation, and lease liability reduction), and provides the balance sheet and P&L impact summary required for financial reporting.

---

## Features

### Core
- Lease designation: mark a lease as an IFRS 16 lease (or exclude using short-term/low-value exemptions)
- Input parameters: commencement date, lease term, incremental borrowing rate, initial direct costs, lease incentives
- ROU asset calculation: initial recognition at present value of lease payments plus initial direct costs
- Lease liability schedule: full period-by-period amortisation with opening balance, interest accrued, payment, and closing balance
- Journal entry generation: opening recognition journals and periodic depreciation, interest, and payment journals
- Transition support: modified retrospective and full retrospective transition methods

### Advanced
- Lease modification: record changes to lease terms (extension, termination, rent change) and recalculate the schedule
- Remeasurement: trigger remeasurement on lease reassessment events
- Multi-currency: calculate and report in both the lease currency and the reporting currency
- Discount rate history: maintain a record of the IBR used at commencement for audit purposes
- Consolidated IFRS 16 report: portfolio-level ROU asset, lease liability, interest, and depreciation summary

### AI-Powered
- IBR suggestion: recommend an appropriate incremental borrowing rate based on lease term and currency
- Modification impact preview: model the financial impact of a proposed lease modification before applying
- Disclosure note drafting: AI drafts the IFRS 16 note to the financial statements from the schedule data

---

## Data Model

```erDiagram
    ifrs16_leases {
        ulid id PK
        ulid lease_id FK
        ulid company_id FK
        date commencement_date
        integer lease_term_months
        decimal incremental_borrowing_rate
        decimal initial_rou_asset
        decimal initial_lease_liability
        decimal initial_direct_costs
        boolean is_short_term_exempt
        boolean is_low_value_exempt
        timestamps created_at_updated_at
    }

    ifrs16_schedule_lines {
        ulid id PK
        ulid ifrs16_lease_id FK
        integer period_number
        date period_date
        decimal opening_liability
        decimal interest_charge
        decimal lease_payment
        decimal closing_liability
        decimal rou_depreciation
        decimal closing_rou_asset
    }

    ifrs16_leases ||--o{ ifrs16_schedule_lines : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `ifrs16_leases` | IFRS 16 lease parameters | `id`, `lease_id`, `commencement_date`, `lease_term_months`, `incremental_borrowing_rate`, `initial_rou_asset` |
| `ifrs16_schedule_lines` | Amortisation schedule | `id`, `ifrs16_lease_id`, `period_date`, `interest_charge`, `closing_liability`, `rou_depreciation` |

---

## Permissions

```
realestate.ifrs16.view
realestate.ifrs16.create
realestate.ifrs16.update
realestate.ifrs16.export-journals
realestate.ifrs16.view-portfolio-summary
```

---

## Filament

- **Resource:** `App\Filament\Realestate\Resources\Ifrs16LeaseResource`
- **Pages:** `ListIfrs16Leases`, `CreateIfrs16Lease`, `EditIfrs16Lease`, `ViewIfrs16Lease`
- **Custom pages:** `Ifrs16SchedulePage`, `JournalExportPage`, `PortfolioIfrs16SummaryPage`
- **Widgets:** `TotalRouAssetWidget`, `TotalLeaseLiabilityWidget`
- **Nav group:** Finance

---

## Displaces

| Feature | FlowFlex | Yardi | CoStar | Nakisa |
|---|---|---|---|---|
| ROU asset calculation | Yes | Yes | No | Yes |
| Full amortisation schedule | Yes | Yes | No | Yes |
| Journal entry generation | Yes | Yes | No | Yes |
| AI IBR suggestion | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[lease-management]] — IFRS 16 leases reference lease records
- [[rental-billing-arrears]] — payment schedule aligns with lease liability amortisation
- [[finance/INDEX]] — journals exported to finance general ledger
