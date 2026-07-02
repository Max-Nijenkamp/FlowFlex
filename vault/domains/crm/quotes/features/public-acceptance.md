---
domain: crm
module: quotes
feature: public-acceptance
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Public Accept / Decline

Prospect accepts or declines a quote via a **signed public link** (no login).

- Tokenised public page *(assumed: Vue page, ui-strategy row #16 — see [[../unknowns]])*; guest guard, throttled.
- Status: `draft → sent → accepted | declined | expired`; validity default 30 days.
- Acceptance pre-fills the deal's products and prompts the rep to close the deal **won** — the invoice
  then flows from the existing `DealWon` path ([[../../../finance/invoicing/_module]]); no separate quote event *(assumed)*.

> [!note] The public quote-accept route was removed with the CRM strip ([[../../../../security/threat-model]]); it returns when this module is rebuilt.

## UI
- **Kind**: public-vue — external unauthenticated, tokenised quote-accept page (ui-strategy row #16).
- **Page**: `Quotes/Accept.vue` at signed route `GET /quote/{quote}/accept?signature=…` (guest guard, throttled), served by `PublicQuoteController`.
- **Layout**: branded read-only quote summary (line items, totals, validity) with Accept / Decline buttons; confirmation panel after action.
- **Key interactions**: prospect clicks Accept or Decline; single-quote-scoped token; expired token/quote shows an "expired" state.
- **States**: empty (n/a) · loading (submitting accept/decline) · error (invalid/expired token, throttled) · selected (accepted/declined confirmation)
- **Gating**: no login — signed-URL signature + rate limiting; token single-quote scoped.

## Data
- Owns / writes: `crm_quotes` — sets status to `accepted | declined` and records the accept/decline timestamp on the quote it owns.
- Reads: `crm_quotes`, `crm_quote_lines` for the public display.
- Cross-domain writes: via events only ([[../../../../security/data-ownership]]) — acceptance pre-fills the deal by firing an event; it never writes `crm_deals` directly.

## Relations
- Consumes: nothing cross-domain (public entrypoint).
- Feeds: `QuoteAccepted` → consumed within-domain by deals to pre-fill products; rep then closes the deal won, and the invoice flows from the existing `DealWon` path *(assumed — no separate quote→invoice event)*.
- Shared entity: the deal (`crm_deals`, owned by [[../../deals/_module|crm.deals]]) — updated via event, read-only here.

## Related

- [[../_module|Quotes]] · [[pdf-generation]] · [[../../deals/_module]]
