---
domain: operations
module: suppliers
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Suppliers — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers)

- **Contact log** — supplier notes/contact log is *(assumed)* a simple text field or lightweight relation. Confirm whether a full activity timeline is wanted (or reuse a shared notes/activity mechanism).
- **Preferred enforcement** — one-preferred-per-item via partial unique index is *(assumed)*.
- **Meilisearch fields** — supplier index (`name`, `contact_name`, `email`) is *(assumed)*.
- **On-time definition** — on-time = GRN `received_at` ≤ PO `expected_delivery`. Confirm tolerance (exact date, or ± grace days).

## Open Questions

- **Multi-currency supplier + item cost** — supplier `currency` and `ops_supplier_items.cost_cents`: is the cost always in the supplier's currency (FX at PO time), or the company base currency? Currently *(assumed)* supplier currency. Ties to the multi-currency purchasing gap in [[../../_opportunities]].
- **Supplier onboarding portal** — should suppliers self-serve a catalogue/price update via a portal (public-vue), or is it internal-only? v1 internal-only *(assumed)*.
