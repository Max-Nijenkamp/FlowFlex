---
domain: support
module: tickets
feature: ticket-merge
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Ticket Merge

Collapse duplicate tickets into one canonical ticket.

## Behaviour

- `TicketService::merge(MergeTicketsData{keep_id, merge_id})`: replies from the source move to the keep ticket; the source is closed and stamped `merged_into_id = keep_id` with a link.
- Both tickets must be open-ish (not already merged); `keep_id ≠ merge_id`.
- Audited via activitylog (both IDs recorded).

## UI

- **Kind**: simple-resource — merge is a row/detail action (with a target-ticket picker modal) on `TicketResource`, not a dedicated page.
- **Page**: action on `TicketResource` / ticket view (`/support/tickets`).
- **Layout**: "Merge into…" action opens a searchable ticket picker; confirm dialog shows what moves.
- **Key interactions**: pick keep target → confirm → replies reassigned, source closed with link banner.
- **States**: empty (no other open tickets to merge into → action disabled) · loading (merge in progress) · error (self/closed target rejected) · selected (target ticket chosen in picker).
- **Gating**: `support.tickets.merge`.

## Data

- Owns / writes: `sup_tickets` (`merged_into_id`, status), `sup_ticket_replies` (reassign `ticket_id`).
- Reads: none cross-domain.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing (a merged/closed source does not fire `TicketResolved`).
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Guard rejects `keep_id == merge_id` and an already-merged source

### Feature (Pest)
- [ ] Merge reassigns all source replies to the keep ticket and stamps the source `merged_into_id = keep_id`, closed
- [ ] A merged/closed source does not fire `TicketResolved`
- [ ] Both tickets locked in one transaction; concurrent double-merge rejected
- [ ] Tenant isolation: cannot merge across companies (source + keep must share `company_id`)

### Livewire
- [ ] "Merge into…" action opens a target picker; self / closed target rejected with an error
- [ ] Action denied without `support.tickets.merge`

## Unknowns

- Whether merge is blocked once either ticket is `closed` — assumed open-ish only. See [[../unknowns]].

## Related

- [[../_module|Tickets]] · [[./ticket-lifecycle]]
