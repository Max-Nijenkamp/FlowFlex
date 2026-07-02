---
domain: crm
module: quotes
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Quotes — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR. Design-affecting items should be resolved before implementation begins.

---

## Open Questions

1. **Invoice stub on acceptance: who triggers it?**
   The spec says "acceptance → deal won → invoice stub via existing DealWon flow; no separate quote event *(assumed)*". This assumes the rep manually marks the deal as won after acceptance, which fires `DealWon`. If auto-trigger is desired, a `QuoteAccepted` event would be needed. Resolve with the invoicing team.

2. **Only one open version per deal?**
   "New version locks old; only one open version per deal *(assumed)*" — clarify whether "open" means draft+sent combined, or only draft. If a rep sends V1 and immediately creates V2 (still draft), are both open?

3. **Decline reason: optional or required?**
   "reason captured *(assumed optional)*" — confirm with product whether a decline reason should be mandatory (useful for reporting) or truly optional.

4. **Version numbering schema**
   `unique (deal_id, quote_number base, version)` *(assumed)* — clarify what "quote_number base" means. Is V2 "Q-2026-001-v2" or a new number "Q-2026-002"?

5. **PDF storage location**
   `pdf_path` is a string — which storage disk? S3-compatible? Local? Not specified.

6. **Public accept page: which Vue guard?**
   "tokenised Vue page, ui-strategy row #16" *(assumed)* — confirm the exact Inertia route and guard name for the public accept page.

---

## Assumed Items (verbatim from spec, unverified)

- `*(assumed)*` — "no separate quote event" on acceptance; invoice flows from `DealWon` only
- `*(assumed: tokenised Vue page, ui-strategy row #16)*` — public accept/decline page implementation
- `*(assumed)*` — `version` unique constraint is `(deal_id, quote_number base, version)` — exact index definition unverified
- `*(assumed optional)*` — decline reason field is optional
- `*(assumed)*` — only one open version per deal at a time
