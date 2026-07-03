---
domain: it
module: helpdesk
feature: self-service-requests
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Self-Service Requests

Employee-facing ticket creation. Any user can raise an IT ticket — report a broken thing (incident) or ask for something (service request) — and track their own tickets. This is the entry point that feeds the staff queue ([[staff-queue]]).

## Behaviour

- Available to **all users** via `it.helpdesk.create-own` — no IT role required.
- Requester chooses **request type**: incident (something is broken) vs service-request (need hardware/software/access).
- Requester picks category (hardware/software/access/network/account) and priority.
- Optional asset link: restricted to the requester's **own assigned assets** *(assumed)* — a user cannot attach someone else's laptop.
- New ticket is `open`; requester sees only their own tickets and their public reply thread.

## UI

- **Kind**: simple-resource (scoped) — the create form + "My tickets" view of `ItTicketResource`, filtered to the current employee.
- **Page**: "Report an IT issue" / "My tickets" (`/it/helpdesk/tickets` → My tickets tab), nav group **Helpdesk**.
- **Layout**: create form — title, description, request_type (incident/service-request toggle), category, priority, optional asset picker (own assets only); list view shows only the requester's tickets with status badge.
- **Key interactions**: submit ticket · view own ticket + public replies · reply to own ticket. No assign / no internal-note controls.
- **States**: empty (no own tickets → "Report your first issue" CTA) · loading (skeleton) · error (validation toast) · selected (own ticket infolist with public thread).
- **Gating**: `it.helpdesk.create-own` (all users); asset picker limited to requester's assigned assets; whole surface gated on `BillingService::hasModule('it.helpdesk')` ([[../security|helpdesk.security]]).

## Data

- Owns / writes: `it_tickets` (create own), `it_ticket_replies` (public replies to own ticket).
- Reads: `hr_employees` (self, requester identity); `it_assets` (the requester's own assigned assets, for the picker).
- Cross-domain writes: none — asset ownership is read from it.assets; requester identity from hr.profiles ([[../../../../security/data-ownership]]).

## Relations

- Consumes: reads own employee record (hr.profiles) + own assigned assets (it.assets).
- Feeds: new-ticket + requester replies surface in [[staff-queue]]; staff public replies notify the requester via core.notifications.
- Shared entity: requester = `hr_employees` (hr.profiles); asset picker source = `it_assets` (it.assets).

## Test Checklist

### Unit
- [ ] Asset picker options are limited to the requester's own assigned assets

### Feature (Pest)
- [ ] Any user with `it.helpdesk.create-own` can create a ticket; it starts `open`, requester = self
- [ ] Requester sees only their own tickets and public replies; internal notes hidden
- [ ] Attaching another employee's asset is rejected

### Livewire
- [ ] Create form validates required fields; assign / internal-note controls not shown to a create-own-only user

## Unknowns

- `*(assumed)*` asset picker restricted to requester's own assigned assets — "assigned to" resolution from it.assets unconfirmed ([[../unknowns|helpdesk.unknowns]]).
- Whether a requester can set priority freely or IT re-triages it is unconfirmed.

## Related

- [[../_module|IT Helpdesk]] · [[ticket-management]] · [[staff-queue]] · [[replies-thread]]
