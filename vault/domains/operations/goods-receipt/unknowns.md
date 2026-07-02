---
domain: operations
module: goods-receipt
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Goods Receipt — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers)

- **10% over-receipt tolerance** — the cumulative over-receipt cap is *(assumed)* 10%. Confirm the value and whether it should be a company setting.
- **`status` enum** — `accepted` / `partially-rejected` as a simple enum is *(assumed)*. Confirm whether a richer status (e.g. `pending-qc`) is needed for a two-step quality workflow.
- **GRN numbering** — per-company sequence format is *(assumed)* (mirror PO numbering).
- **Meilisearch fields** — GRN index (`grn_number`, PO number) is *(assumed)*.
- **Cost source** — accepted stock posts at the **PO line cost**. Confirm behaviour when the actual invoiced cost later differs (price variance handling is a finance/3-way-match concern).

## Open Questions

- **Two-step quality check** — v1 accepts/rejects inline at receipt. Should there be a separate quarantine → QC → accept flow (goods land in a `virtual`/quarantine warehouse first)? Deferred *(assumed)*; ties to the `virtual` warehouse question in [[../warehouses/unknowns]].
- **Returns to supplier** — rejected goods: is a supplier return / debit-note flow needed, or is recording the rejection enough for v1? Currently record-only *(assumed)*.
