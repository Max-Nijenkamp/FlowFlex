---
domain: hr
module: shift-scheduling
feature: swap-requests
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Swap Requests

## Purpose

Let an employee swap a shift with a colleague, subject to manager approval.

## Intended Behavior

- `requestSwap(RequestSwapData)` — requester picks own shift + a recipient with no conflict on that date; status `pending`.
- `acceptSwap(...)` — recipient accepts; status `accepted`.
- `approveSwap(...)` — manager approves; status `approved`, `manager_approved_at` set, shifts reassigned.
- Declines terminate the request. State flow diagram in [[../architecture]].
- Surfaced via `ShiftSwapRequestResource` (CRUD, ui-strategy #1) with an approve action listing pending swaps.

## Tables / Permissions / Events

- Tables: `hr_shift_swap_requests`, `hr_shifts`
- Permissions: `hr.shifts.request-swap`, `hr.shifts.approve-swap`
- Events: none fired

## UI

- **Kind**: simple-resource (`ShiftSwapRequestResource` with approval actions)
- **Page**: "Swap Requests" (`/hr/shift-swap-requests`)
- **Layout**: table — requester, recipient, shift, status badge (pending/accepted/approved/declined); manager view lists pending swaps with an approve action; employee view shows own requests and incoming swaps to accept/decline.
- **Key interactions**: `requestSwap` (pick own shift + recipient); recipient `acceptSwap`; manager `approveSwap` (reassigns shifts, sets `manager_approved_at`); decline terminates.
- **States**: empty ("No swap requests") · loading (table skeleton) · error (toast on conflict / invalid transition) · selected (row opens swap detail with accept/approve/decline).
- **Gating**: request requires `hr.shifts.request-swap`; approve requires `hr.shifts.approve-swap`.

## Data

- Owns / writes: `hr_shift_swap_requests` (writes `hr_shifts` reassignment on approval, within this module)
- Reads: reads `hr_employees` via EmployeeService (requester/recipient, conflict check on recipient's date)
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none
- Shared entity: `hr_employees` (read via EmployeeService)

## Related

- [[../_module]] · [[../architecture]] · [[../api]]
