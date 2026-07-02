---
domain: finance
module: accounts-payable
feature: three-way-match
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# 3-Way Match

Gate payment on a match between **bill ↔ purchase order ↔ goods receipt** before a supplier is paid.

- Active only when Procurement/Operations is built ([[../../../operations/purchase-orders/_module]] +
  [[../../../operations/goods-receipt/_module]]); **bypassed** when those modules are inactive (manual bills).
- Consumes `GoodsReceived` ([[../../../../architecture/event-bus]]) → drafts a bill and attempts the match.
- Mismatch (qty/price/PO) blocks scheduling → `MatchFailedException`.

> [!note] Depends on unbuilt modules — this is a rebuild target, not current behavior.

## UI

- **Kind**: background (listener-driven) + match-status surface on `BillResource`
- **Page**: no dedicated page — match status shown as a badge/panel on the bill in `BillResource` (`/finance/ap/bills`)
- **Layout**: match-status indicator on the bill view (matched / mismatch reason)
- **Key interactions**: match runs automatically on `GoodsReceived`; a mismatch blocks scheduling and surfaces the reason on the bill
- **States**: empty (no PO/GRN to match) · loading (match in progress) · error (`MatchFailedException` — scheduling blocked) · selected (bill with match detail)
- **Gating**: `finance.ap.view-any`

## Data

- Owns / writes: `fin_bills` (drafts a bill from the receipt, sets match status). Money as integer minor units (cents) via brick/money.
- Reads: PO / GRN via [[../../../operations/purchase-orders/_module]] + [[../../../operations/goods-receipt/_module]] read APIs (never their tables)
- Cross-domain writes: none — only writes own `fin_bills`; mismatch blocks scheduling rather than posting ([[../../../../security/data-ownership]])

## Relations

- Consumes: `GoodsReceived` from operations/procurement (goods-receipt) → drafts a bill + attempts the match; mismatch → `MatchFailedException` blocks scheduling
- Feeds: matched draft bills flow into [[bill-approval]]
- in-domain: hands bills to [[bill-approval]] / [[payment-runs]]

> [!warning] UNVERIFIED
> Depends on unbuilt Procurement/Operations modules (`purchase-orders`, `goods-receipt`). The `GoodsReceived` payload and PO/GRN read-API contracts do not exist yet — they will be added when procurement is built. Treat this whole dependency as speculative.

## Related

- [[../_module|Accounts Payable]] · [[bill-approval]] · [[payment-runs]]
