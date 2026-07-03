---
domain: finance
module: accounts-payable
feature: ap-aging
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# AP Aging Report

Bucket outstanding supplier liabilities by age: **current, 30, 60, 90+ days**.

- Custom Filament page (`ApAgingPage`) over unpaid `fin_bills`.
- Drives payment prioritisation + the early-payment-discount window.
- Read-only; permission `finance.ap.view-any`.

## UI

- **Kind**: custom-page (report)
- **Page**: "AP Aging" (`ApAgingPage`) under `/finance/ap/aging`
- **Layout**: read-only bucket table — one row per supplier/bill, columns `current | 30 | 60 | 90+`, totals footer
- **Key interactions**: filter by supplier/date, drill into a bill; drives payment prioritisation + the early-payment-discount window (no writes)
- **States**: empty (no unpaid bills) · loading (skeleton table) · error (query failure) · selected (drill into a bill row)
- **Gating**: `finance.ap.view-any`

## Data

- Owns / writes: nothing — reads its own module tables only (`fin_bills`, unpaid). Money as integer minor units (cents) via brick/money.
- Reads: `fin_bills` (own tables, read-only aggregation into age buckets)
- Cross-domain writes: none — report is purely read-only ([[../../../../security/data-ownership]])

## Relations

- Consumes: no events
- Feeds: no events (surfaces data for human decisions only)
- in-domain: reads bills produced by [[bill-approval]]; informs [[payment-runs]] prioritisation

## Test Checklist

### Unit
- [ ] Age bucketing assigns an unpaid bill to `current | 30 | 60 | 90+` at boundary days (30/31, 60/61, 90/91) from its due date
- [ ] Per-bucket and footer totals sum via brick/money (integer minor units, no float math)

### Feature (Pest)
- [ ] `aging()` buckets only unpaid `fin_bills` and excludes paid/voided bills from GL fixtures
- [ ] Tenant isolation: company A's aging query never includes company B bills

### Livewire
- [ ] `ApAgingPage` renders the bucket table and drills into a bill row; `canAccess` denied without `finance.ap.view-any`

## Related

- [[../_module|Accounts Payable]] · [[payment-runs]] · [[../../accounts-receivable/_module|AR (aging counterpart)]]
