---
type: module
domain: Real Estate & Property Management
panel: realestate
cssclasses: domain-realestate
phase: 6
status: complete
migration_range: 963000–964999
last_updated: 2026-05-12
---

# IFRS 16 Lease Accounting

Calculates right-of-use (ROU) assets and lease liabilities for all incoming leases under IFRS 16 (or AASB 16 / ASC 842 for AU/US). Generates amortisation schedules and journal entries for Finance.

---

## Background

IFRS 16 (effective 2019) requires lessees to bring virtually all leases onto the balance sheet:
- **Right-of-use asset**: the value of the right to use the leased asset for the lease term
- **Lease liability**: present value of future lease payments

Prior to IFRS 16, most operating leases were off-balance-sheet (only disclosed in notes). IFRS 16 eliminated this for listed companies; FRS 102 (UK private) will follow.

---

## Recognition

On lease commencement (or on transition date for existing leases):

**Initial measurement:**
```
Lease Liability = PV of future lease payments discounted at IBR (Incremental Borrowing Rate)

Right-of-Use Asset = Lease Liability
  + Initial direct costs
  + Prepaid rent payments
  + Lease incentives (restoration obligations)
  − Lease incentives received (rent-free periods)
```

**IBR (Incremental Borrowing Rate)**: rate the company would pay to borrow funds to purchase the asset. Set per lease — often based on company's latest bank facility rate adjusted for lease term.

---

## Amortisation Schedule

Monthly schedule computed for each lease:

| Month | Opening Liability | Interest (IBR×) | Payment | Closing Liability |
|---|---|---|---|---|
| Jan 2026 | 1,200,000 | 5,000 | (25,000) | 1,180,000 |
| Feb 2026 | 1,180,000 | 4,917 | (25,000) | 1,159,917 |
| ... | | | | |

ROU Asset depreciation: straight-line over shorter of lease term or useful life of underlying asset.

---

## Journal Entries

Auto-generated monthly:

```
Commencement:
  DR Right-of-Use Asset    1,200,000
    CR Lease Liability         1,200,000

Monthly:
  DR Lease Liability          20,083   (payment - interest)
  DR Finance Cost (P&L)        4,917   (interest)
    CR Cash / AP               25,000  (lease payment)

  DR Depreciation (P&L)       20,000   (ROU asset / lease term)
    CR Accumulated Depreciation 20,000
```

Pushed as draft journal entries to Finance GL module for review and posting.

---

## Lease Modifications

When lease terms change (rent review, extension, early termination):
- Remeasure lease liability using revised future payments + new IBR
- Adjust ROU asset by same amount
- Log modification event with original and revised schedules

### Short-Term & Low-Value Exemptions (IFRS 16.5)
- **Short-term**: lease term ≤ 12 months at commencement → expense as incurred, no ROU/liability
- **Low-value**: underlying asset value ≤ USD 5,000 when new → expense as incurred

Exemptions flagged per lease. System prompts if lease qualifies.

---

## Disclosure Pack

For annual report notes (manually reviewed before filing):
- Maturity analysis of lease liabilities: <1yr / 1–5yr / >5yr
- Total interest expense on lease liabilities
- Total depreciation of ROU assets
- Total cash outflow from leases
- Weighted average IBR used

---

## Data Model

### `realestate_ifrs16_leases`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| lease_id | ulid | FK `realestate_leases` |
| ibr_pct | decimal(6,4) | e.g. 4.25 |
| commencement_date | date | |
| lease_term_months | int | |
| initial_roa | decimal(16,2) | right-of-use asset at commencement |
| initial_liability | decimal(16,2) | |
| gl_roa_account | varchar(20) | FK to CoA account |
| gl_liability_account | varchar(20) | |
| gl_depreciation_account | varchar(20) | |
| gl_finance_cost_account | varchar(20) | |
| exemption | enum | none/short_term/low_value |
| status | enum | active/modified/terminated/expired |

---

## Migration

```
963000_create_realestate_ifrs16_leases_table
963001_create_realestate_ifrs16_schedules_table
963002_create_realestate_ifrs16_journal_entries_table
```

---

## Related

- [[MOC_RealEstate]]
- [[lease-management]] — lease terms source
- [[general-ledger-chart-of-accounts]] — journal entry destination
- [[MOC_Finance]] — ROU asset on balance sheet
