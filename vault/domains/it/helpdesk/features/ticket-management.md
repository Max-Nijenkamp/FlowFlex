---
domain: it
module: helpdesk
feature: ticket-management
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Ticket Management

The core CRUD surface for IT tickets — list, create, edit, view. My-tickets vs. all-tickets tabs, with category, priority and status columns/filters. The IT-staff triage board is a separate custom page ([[staff-queue]]).

## Behaviour

- Ticket carries title, description, requester (employee), category, request type, priority, assignee, status.
- Status machine `open → in_progress → resolved → closed` ([[../architecture|helpdesk.architecture]]).
- Two tabs: **My tickets** (`requester_employee_id` = current employee) and **All** (visible only with `it.helpdesk.view-any`).
- Assignment sets `assignee_id` and moves `open → in_progress`.
- Categories: hardware · software · access · network · account. Priorities: urgent · high · normal · low.

## UI

- **Kind**: simple-resource — `ItTicketResource` (table + form + infolist).
- **Page**: "IT tickets" (`/it/helpdesk/tickets`), nav group **Helpdesk**.
- **Layout**: table columns — ticket_number, title, category, priority (badge), status (badge), assignee, updated_at. Tabs: **My tickets** / **All** (All requires `it.helpdesk.view-any`). Form fields: title, description, category, request_type, priority, asset link, assignee (staff only).
- **Key interactions**: create ticket · edit/assign (staff) · open infolist with reply thread ([[replies-thread]]) · filter by category/priority/status/assignee.
- **States**: empty (no tickets → "No tickets yet" + create CTA) · loading (table skeleton) · error (toast + retry) · selected (row → infolist / edit page).
- **Gating**: list/create via `it.helpdesk.create-own`; the **All** tab + assign field via `it.helpdesk.view-any` / `it.helpdesk.assign`. Whole resource gated on `BillingService::hasModule('it.helpdesk')` ([[../security|helpdesk.security]]).

## Data

- Owns / writes: `it_tickets` (+ `it_ticket_replies` via the thread relation).
- Reads: `hr_employees` (requester name/dept, hr.profiles); `it_assets` (soft asset link, it.assets).
- Cross-domain writes: none — requester and asset data are read-only; status notifications go via core.notifications, never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing directly (reads employee + asset data at display time).
- Feeds: resolve/public-reply → requester notification via core.notifications.
- Shared entity: requester = `hr_employees` (owned by hr.profiles); `asset_id` = `it_assets` (owned by it.assets).

## Test Checklist

### Unit
- [ ] `category` / `request_type` / `priority` must be in their allowed sets

### Feature (Pest)
- [ ] Create ticket stamps sequential `ticket_number`, `status = open`, requester = current employee
- [ ] Assign sets `assignee_id` + transitions `open → in_progress`
- [ ] My-tickets tab shows only own tickets; All tab requires `it.helpdesk.view-any`; company B tickets never visible

### Livewire
- [ ] `ItTicketResource` All tab + assign field hidden without `it.helpdesk.view-any` / `it.helpdesk.assign`; form validates required fields

## Unknowns

- `*(assumed)*` requester may only link their own assigned asset; staff-vs-requester link scope unconfirmed ([[../unknowns|helpdesk.unknowns]]).
- `*(assumed)*` sequential ticket-number generation mechanism.

## Related

- [[../_module|IT Helpdesk]] · [[staff-queue]] · [[self-service-requests]] · [[replies-thread]] · [[../../../../architecture/patterns/states]]
