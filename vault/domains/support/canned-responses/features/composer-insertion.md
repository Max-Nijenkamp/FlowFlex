---
domain: support
module: canned-responses
feature: composer-insertion
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Composer Insertion

Insert a template into a reply with `/shortcut`, substituting live ticket variables.

## Behaviour

- Type `/shortcut` in the ticket reply composer → autocomplete of matching own+shared templates.
- On select, `RenderCannedResponseAction` substitutes `{{customer_name}}`, `{{agent_name}}`, `{{ticket_number}}` from the current ticket (unknown tokens left literal *(assumed)*) and increments `usage_count`.
- Same mechanism reused in the chat composer (P3).

## UI

- **Kind**: custom-page (embedded action) — the insertion is an autocomplete widget inside the [[../../tickets/features/ticket-inbox|Ticket Inbox]] composer, not a standalone page.
- **Page**: action within `TicketInboxPage` / ticket reply composer.
- **Layout**: `/`-triggered dropdown listing shortcut + title preview; insert replaces the trigger text with the rendered body.
- **Key interactions**: `/` triggers list; arrow/enter to insert; variables resolved from ticket context; usage counter bumps.
- **States**: empty (no matching shortcut → "no canned responses") · loading (fetch) · error (render fails → plain shortcut left) · selected (item highlighted in dropdown).
- **Gating**: `support.canned.view-any` (+ `support.tickets.reply` to be in the composer at all).

## Data

- Owns / writes: `sup_canned_responses` (`usage_count`).
- Reads: `sup_tickets` fields at render time (same domain, read-only for the values).
- Cross-domain writes: none — writes only its own usage counter ([[../../../../security/data-ownership]]).

## Relations

- Consumes: current ticket context (customer/agent/number).
- Feeds: rendered body into the [[../../tickets/_module|support.tickets]] reply (and chat, P3).
- Shared entity: `sup_tickets` (read for variables).

## Unknowns

- Chat composer insertion timing (P3) — [[../unknowns]].

## Related

- [[../_module|Canned Responses]] · [[./response-templates]] · [[../../tickets/features/ticket-inbox]]
