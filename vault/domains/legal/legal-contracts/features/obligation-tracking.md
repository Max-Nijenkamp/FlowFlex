---
domain: legal
module: legal-contracts
feature: obligation-tracking
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Obligation Tracking

Track deliverables and payment milestones tied to a contract, with due dates and overdue alerts.

## Behaviour

- Each obligation: description, due_date, responsible user, status (`open / done / overdue`).
- Lifecycle command flips past-due `open` obligations to `overdue` and fires a one-time alert (`alerted` guard).
- Marking done closes the obligation.

## UI

- **Kind**: simple-resource — obligations surface as a relation-manager tab inside `LegalContractResource` (not a standalone screen).
- **Page**: obligations tab on the contract view (`/legal/contracts/{id}`).
- **Layout**: table of obligations (description, due date, responsible, status badge); inline add form.
- **Key interactions**: add obligation; mark done; filter overdue; assign responsible user.
- **States**: empty ("No obligations tracked") · loading (skeleton rows) · error (validation) · selected (row → edit).
- **Gating**: `legal.contracts.update`.

## Data

- Owns / writes: `legal_contract_obligations`.
- Reads: `users` for the responsible assignee (platform).
- Cross-domain writes: none — overdue alerts via `core.notifications` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: overdue-obligation notifications via `core.notifications`.
- Shared entity: `users` (platform).

## Test Checklist

### Unit
- [ ] Overdue rule flips `open` obligations past `due_date` to `overdue`; `done` obligations untouched
- [ ] `alerted` guard prevents a second overdue alert for the same obligation

### Feature (Pest)
- [ ] Add obligation to a contract persists with responsible user and `open` status
- [ ] Lifecycle command fires a single overdue notification per obligation via `core.notifications`
- [ ] Mark done closes the obligation and stops further overdue evaluation
- [ ] Company A cannot add/read company B obligations (CompanyScope via parent contract)

### Livewire
- [ ] Obligations relation-manager add form validates required fields and gates on `legal.contracts.update`
- [ ] Overdue filter returns only overdue rows within tenant scope

## Unknowns

- No typed obligation kind (deliverable vs payment) in v1 — [[../unknowns]].

## Related

- [[../_module|Legal Contracts]] · [[./contract-lifecycle]]
