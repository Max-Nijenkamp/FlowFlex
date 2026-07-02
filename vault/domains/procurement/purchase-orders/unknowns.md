---
domain: procurement
module: purchase-orders
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Procurement PO Layer — Open Questions

- **`procurement_approved_at` placement.** Column on `ops_purchase_orders` vs a separate `proc_po_approval` table vs event-applied by Operations. **UNVERIFIED** — needs an ADR ([[decisions]]).
- `PurchaseApproved` payload — does finance need line detail or just header total? `*(assumed: header total + po_id)*`
- Commitment definition edge cases: partial receipts, cancelled POs, FX at commit vs receipt. `*(assumed: committed = sent − received at PO currency)*`
- Multi-round sourcing / RFQ to suppliers (vs manually entered quotes). `*(assumed: manual quote entry v1)*` — differentiator ([[../_opportunities]]).
- PO approval category source (header vs originating requisition). `*(assumed: from requisition)*`

## Related

- [[_module]] · [[decisions]]
