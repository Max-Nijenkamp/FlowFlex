---
domain: procurement
module: purchase-orders
feature: sourcing
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Sourcing / Quote Comparison

Collect and compare supplier quotes for a PO side-by-side, then select the winner. Selecting sets the PO's supplier (draft only).

## Behaviour

- Add quotes: supplier (not blacklisted), amount, reference.
- Compare quotes side-by-side (amount, lead time from catalogue, supplier rating/status).
- Select one quote → `selected = true` (exactly one per PO); updates the PO supplier via Operations' service; reprices *(assumed)*.
- Supplier swap allowed in draft only.

## UI

- **Kind**: custom-page
- **Page**: "Sourcing board" (`/operations/procurement/purchase-orders/{po}/sourcing`)
- **Layout**: side-by-side quote comparison cards/columns per supplier (amount, lead time, status badge); "select" per column; add-quote form/slide-over.
- **Key interactions**: add quote → card appears; select → confirm supplier swap → optimistic highlight + PO supplier updates; blacklisted suppliers not selectable.
- **States**: empty ("No quotes yet — add the first") · loading (card skeletons) · error (toast + retry) · selected (winning card highlighted, others dimmed).
- **Gating**: `procurement.purchase-orders.source`.

## Data

- Owns / writes: `proc_po_sourcing`.
- Reads: `SupplierGate::isBlocked` + catalogue lead times ([[../../supplier-catalogue/_module|catalogue]]); PO header from Operations.
- Cross-domain writes: **none** — supplier change on the PO goes through Operations' service ([[../../../../security/data-ownership]]).

## Relations

- Consumes: catalogue + supplier status ([[../../supplier-catalogue/_module]]); PO from Operations.
- Feeds: selected supplier → PO (via Operations); winning quote → commitment/savings.

## Unknowns

- Multi-round RFQ / send quote requests to suppliers vs manual entry. `*(assumed: manual v1)*` — differentiator ([[../../_opportunities]]).

## Related

- [[../_module|Procurement PO Layer]] · [[../../supplier-catalogue/features/supplier-status]] · [[po-approval]]
