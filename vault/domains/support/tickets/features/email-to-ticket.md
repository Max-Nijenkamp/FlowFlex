---
domain: support
module: tickets
feature: email-to-ticket
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Email-to-Ticket

Inbound customer emails become tickets (or threaded replies) via a signed parse webhook.

## Behaviour

- Provider inbound-parse webhook *(assumed: Resend/Postmark)* POSTs to `InboundEmailController`.
- Signature-verified per [[../../../../security/webhooks-signing]]; reject unsigned/invalid; rate-limited.
- `TicketService::handleInboundEmail(payload)`: if the subject / `References` header matches an existing `ticket_number` → append a customer reply (and `waiting_on_customer → in_progress`); otherwise create a new ticket (find-or-create requester by email via `ContactService`).
- Body + attachments purified/whitelisted before storage.

## UI

- **Kind**: background — API-only endpoint, no agent-facing page. Resulting tickets appear in the [[./ticket-inbox|inbox]] / `TicketResource`.
- **Trigger**: `POST /webhooks/support/inbound-email` (signed).

## Data

- Owns / writes: `sup_tickets`, `sup_ticket_replies`.
- Reads: `crm_contacts` via `ContactService` (find-or-create requester, soft).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: inbound email webhook (foundation.email inbound parse).
- Feeds: a threaded customer reply resumes the SLA clock (read by [[../../sla/_module|support.sla]]).
- Shared entity: `crm_contacts` (requester email match).

## Test Checklist

### Unit
- [ ] Subject / `References` header parse resolves an existing `ticket_number` vs. a new-ticket case
- [ ] Body + attachments are purified / MIME-whitelisted before storage

### Feature (Pest)
- [ ] Unmatched inbound email creates a new ticket, find-or-creating the requester by email via `ContactService`
- [ ] Inbound email matching an existing ticket appends a customer reply and flips `waiting_on_customer → in_progress`
- [ ] Unsigned / invalid-signature webhook is rejected; valid signature required (never `*(assumed)*`)
- [ ] Tenant isolation: an inbound email routes only to the addressed company's ticket namespace

## Unknowns

- Inbound provider + payload shape *(assumed)* — [[../unknowns]].

## Related

- [[../_module|Tickets]] · [[../api|Tickets API]] · [[../security|Tickets Security]]
