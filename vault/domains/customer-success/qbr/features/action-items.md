---
domain: customer-success
module: qbr
feature: action-items
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Action Items

Capture follow-up commitments from a QBR with owners and due dates, and remind owners when items go overdue.

## Behaviour

- On `complete(RecordOutcomesData)`, action items are created from the outcomes form: `{description, owner_id, due_date}`.
- Each item tracks `status` (open / done).
- `QbrActionReminderCommand` (daily) reminds the owner of overdue open items once, guarded by `reminded`.
- Items are managed via a relation on the QBR record.

## UI

- **Kind**: simple-resource — a relation manager on `QbrResource` (action items belong to a QBR).
- **Page**: within QBR detail at `/crm/qbrs/{qbr}` → "Action items" relation.
- **Layout**: table (description, owner, due_date, status); inline add/edit; overdue rows highlighted.
- **Key interactions**: add item · mark done · reassign owner / change due date · filter open/overdue.
- **States**: empty (no items → "add follow-ups from this review") · loading (relation skeleton) · error (due date past / missing owner → validation) · selected (item editing). Overdue = red highlight.
- **Gating**: `cs.qbr.view-any` to view; `cs.qbr.manage` to add/edit/complete.

## Data

- Owns / writes: `cs_qbr_action_items` (own table only).
- Reads: owner (user) reference for assignment/display — read-only.
- Cross-domain writes: none — overdue reminders dispatch via `core.notifications` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: created by QBR completion ([[./qbr-scheduling|QBR Scheduling]]).
- Feeds: `core.notifications` (overdue reminders).
- Shared entity: owner user record (read-only).

## Test Checklist

### Unit
- [ ] Action item requires owner + due date; `reminded` guard blocks duplicate reminders

### Feature (Pest)
- [ ] `QbrActionReminderCommand` reminds overdue OPEN items once; completed items never reminded
- [ ] Tenant isolation: items per company; edit gated

### Livewire
- [ ] Items relation manager adds/completes items; overdue badge renders

## Unknowns

- Reminders as notifications (not events) — [[../unknowns]].

## Related

- [[../_module|QBR]] · [[./qbr-scheduling|QBR Scheduling]] · [[./deck-preparation|Deck Preparation]]
