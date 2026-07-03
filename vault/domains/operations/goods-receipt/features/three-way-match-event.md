---
domain: operations
module: goods-receipt
feature: three-way-match-event
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: GoodsReceived Event (3-Way Match)

Fire `GoodsReceived` on acceptance so Finance AP can draft a supplier bill and run a PO ↔ GRN ↔ bill match.

## Behaviour

- Fired inside the `GrnService::receive` transaction, after stock + PO updates.
- Payload carries `company_id` (scalar), `grn_id`, `po_id`, `supplier_id`, `accepted_total_cents`, `currency`, `received_at` — **accepted totals only** (rejected never bills).
- finance.ap's own listener (`ShouldQueue` + `WithCompanyContext`) drafts a bill and links it to the PO + GRN for the 3-way match.
- If finance.ap is inactive, the event fires unconsumed (no error).

## UI

- **Kind**: background — no UI in this module. The event is emitted; the 3-way match surface lives in finance.ap.
- **Trigger**: `GoodsReceived` fired by `GrnService::receive`.

## Data

- Owns / writes: nothing beyond the GRN rows written by the receiving feature.
- Reads: accepted line totals to build the payload.
- Cross-domain writes: **none** — finance.ap's listener writes the draft bill in finance tables; this module never writes finance data ([[../../../../security/data-ownership]]). Payload contract: [[../../../../architecture/event-bus]].

## Relations

- Consumes: nothing.
- Feeds: `GoodsReceived` → finance.ap (draft bill + 3-way match).
- Shared entity: `supplier_id` maps to finance.ap's supplier via `ops_suppliers.fin_supplier_id`.

## Test Checklist

### Unit
- [ ] Payload builder includes accepted totals only and carries `company_id` as a scalar (never a model)

### Feature (Pest)
- [ ] `GoodsReceived` fires inside the receive transaction after stock + PO updates (not before)
- [ ] With finance.ap inactive the event fires unconsumed and raises no error
- [ ] `accepted_total_cents` computed via brick/money at PO line cost (no float math)

<!-- background event feature — no Livewire surface in this module -->

## Related

- [[../_module|Goods Receipt]] · [[../../../finance/accounts-payable/_module|finance.ap]] · [[../../../../architecture/event-bus]]
