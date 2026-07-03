---
domain: customer-success
module: qbr
feature: qbr-scheduling
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# QBR Scheduling

Schedule a Quarterly Business Review for an account, track its status, and auto-create the next one on completion per cadence.

## Behaviour

- `schedule(ScheduleQbrData)` creates a `scheduled` QBR (account, future `scheduled_at`, csm, agenda defaulted from template *(assumed)*).
- Status flow: `scheduled → held` (requires outcomes, via [[./deck-preparation|deck prep]] + record-outcomes) or `scheduled → cancelled`.
- On completion, the next QBR is auto-created a quarter out *(assumed cadence)*.
- QBR history per account is available by filtering.

## UI

- **Kind**: simple-resource — `QbrResource`.
- **Page**: "QBRs" at `/crm/qbrs` (Customer Success nav group).
- **Layout**: table (account, scheduled_at, status, csm); form = account + date + csm + agenda; status badge; account-history filter.
- **Key interactions**: schedule QBR · Prepare deck / Record outcomes actions · cancel · filter by account/status.
- **States**: empty (no QBRs → "schedule the first review") · loading (table skeleton) · error (date in past → validation; outcomes missing on held → reject) · selected (QBR opened).
- **Gating**: `cs.qbr.view-any` to view; `cs.qbr.manage` to schedule / change status.

## Data

- Owns / writes: `cs_qbrs` (own table only).
- Reads: account + owner via `crm.contacts` read API — never CRM tables.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: `cs.analytics` (QBR cadence adherence).
- Shared entity: `crm_accounts` (read-only) + owner (CSM).

## Test Checklist

### Unit
- [ ] State machine: scheduled->held requires outcomes; scheduled->cancelled allowed; held is terminal

### Feature (Pest)
- [ ] `complete` writes outcomes + creates action items + auto-creates next QBR per cadence exactly once (locked transition)
- [ ] Tenant isolation + permission: schedule/complete/cancel verbs enforced

### Livewire
- [ ] Schedule form validates account + date; complete action requires outcomes before transition

## Unknowns

- Cadence configurability + cancel-vs-complete chaining — [[../unknowns]].

## Related

- [[../_module|QBR]] · [[./deck-preparation|Deck Preparation]] · [[./action-items|Action Items]]
