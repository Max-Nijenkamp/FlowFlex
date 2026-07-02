---
domain: customer-success
module: qbr
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# QBR — Architecture

## Services & Actions

Interface→Service: `QbrServiceInterface` → `QbrService`.

- `prepareDeck(string $qbrId): void` — snapshots active-source metrics (health trend, support summary, deal/contract overview) into `deck_data`. Sections whose soft-dep module is inactive are omitted, not zeroed.
- `complete(RecordOutcomesData): Qbr` — requires `outcomes`; transitions to `held`, creates the action items, and auto-creates the next QBR per cadence (quarterly default *(assumed)*).
- `schedule(ScheduleQbrData): Qbr` — creates a `scheduled` QBR.

`QbrActionReminderCommand` — daily; reminds owners of overdue open action items once (`reminded` guard).

State: `scheduled → held` (needs outcomes) / `scheduled → cancelled`.

---

## Events

### Fires
None v1. Action-item reminders are `core.notifications`, not cross-domain events *(assumed)*.

### Consumes
None v1. Deck prep is user- or schedule-initiated *(assumed)*.

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `QbrActionReminderCommand` | notifications | daily | `reminded` flag, once per overdue item |

Deck prep may run on demand (button) or as a pre-QBR scheduled task *(assumed)*. Full queue context in [[../../../architecture/queue-jobs]].

---

## Filament Artifacts

**Nav group:** Customer Success

| Artifact | Kind ([[../../../architecture/patterns/feature-ui-spec]]) | Notes |
|---|---|---|
| `QbrResource` | simple-resource | schedule; **Prepare deck** + **Record outcomes** actions; action-items relation manager; deck_data shown as an infolist; QBR history per account via filter |

**Access contract:** `canAccess() = Auth::user()->can('cs.qbr.view-any') && BillingService::hasModule('cs.qbr')` per [[../../../architecture/filament-patterns]] #1. Create/schedule + record-outcomes require `cs.qbr.manage`. No public/portal surface.

---

## Search & Realtime

- Search: none.
- Realtime: none — deck reflects the last prep snapshot.

---

## Security Notes

- No encrypted fields; no public endpoints; no rate limiter (internal panel actions only).

See [[./security]] for the full access contract and permissions.
