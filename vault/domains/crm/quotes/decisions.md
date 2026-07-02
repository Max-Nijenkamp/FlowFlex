---
domain: crm
module: quotes
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Quotes — Decisions

---

## Interface→Service Pattern

`QuoteServiceInterface` → `QuoteService` is used (not a simple action) because the quote lifecycle spans multiple complex methods (`createFromDeal`, `send`, `accept`, `decline`, `newVersion`) that share internal state helpers. The interface allows mocking in tests and future swapping (e.g. for a CPQ-based implementation when crm.pricing ships).

---

## No Standalone Quote Event on Acceptance

Quote acceptance does **not** fire a dedicated domain event. Instead, the rep is prompted to mark the deal as Won, which fires the existing `DealWon` event that the invoicing module already consumes. Rationale: avoids duplicating the deal-won side-effects. See [[unknowns|quotes.unknowns]] — this is marked *(assumed)* and may need an ADR if the invoicing team disagrees.

---

## Versioning: Lock Old, Create New Draft

When `newVersion` is called on a sent (or draft) quote, the existing quote is set to a read-only locked state and a new draft is created with `version + 1`. Only one open (draft or sent) version per deal is planned *(assumed)* — see [[unknowns|quotes.unknowns]].

---

## Quote Number Assigned at Send, Not Create

Quote number (Q-2026-001) is assigned when the quote transitions from `draft` to `sent`, not at creation. Rationale: avoids gaps in the number sequence from abandoned drafts.

---

## Totals via brick/money

All monetary arithmetic uses brick/money for consistent rounding. Line totals: `quantity × unit_price_cents` reduced by `discount_percent`, then summed. Quote discount applied to subtotal. Tax calculated from tax_rate if the Tax module is active; otherwise a default rate or zero. Stored as integer cents in all columns.

---

## PDF Path Access

The PDF at `pdf_path` is not served directly from storage — it must go through `PublicQuoteController` to validate the token and apply rate limiting. Direct storage URLs are not exposed.

---

## Security Audit Note (HIGH)

From the 2026-06-11 security audit: the public quote route **must** run on a guest guard with no app-session bleed. The `accept_token` (UUID) must be validated and rate-limited. See [[security|quotes.security]] for the full contract.
