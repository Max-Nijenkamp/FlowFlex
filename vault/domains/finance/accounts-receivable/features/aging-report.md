---
domain: finance
module: accounts-receivable
feature: aging-report
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — AR Aging Report

`ArAgingPage` (#9 report custom page, [[../../../../architecture/ui-strategy]]): open balances bucketed by age, drillable to invoices.

- Buckets: current, 1–30, 31–60, 61–90, 90+ days overdue.
- Backed by `ArService::aging(?customerId): ArAgingData`, computed from `fin_invoices` + `fin_payments`.
- Boundary correctness is a test target (30/31, 90/91 days).
- Drill an account → its open invoices.
- Result cached as `company:{id}:finance:ar-aging` (1 h); busted by the `InvoicePaid` listener, payment allocation, and write-off.
- DSO and AR turnover are derived alongside the buckets (`ArService::dso(period)`, brick/money).

## UI
- **Kind**: custom-page
- **Page**: "AR Aging" — `ArAgingPage` (`/finance/ar/aging`)
- **Layout**: bucket columns (current · 1–30 · 31–60 · 61–90 · 90+), each cell drillable to the underlying invoices; DSO + AR turnover KPIs pinned at top
- **Key interactions**: pick an optional customer filter; drill an account → its open invoices
- **States**: empty (no open balances — buckets show zero) · loading (cache miss recompute) · error (`ArService::aging` failure surfaced on page) · selected (customer/bucket drilled, invoice list shown)
- **Gating**: `finance.ar.view-any`

## Data
- Owns / writes: no tables — this is a read/report feature (result cached `company:{id}:finance:ar-aging`, 1 h; amounts as integer minor units / cents via brick/money)
- Reads: `fin_invoices` + `fin_payments` from finance.invoicing (read-only)
- Cross-domain writes: none — reads invoicing tables only, never writes them ([[../../../../security/data-ownership]])

## Relations
- Consumes: `InvoicePaid` from finance.invoicing → busts the `ar-aging` cache
- Feeds: nothing
- In-domain: `ArService::aging`, `ArService::dso(period)` compute the buckets and KPIs

See [[../api]], [[../data-model]].
