---
domain: procurement
module: approvals
feature: escalation
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# SLA Escalation

Approvals not actioned within `escalation_days` auto-escalate: notify the next level plus the original approver's manager.

## Behaviour

- Daily scheduled scan of pending approvals across consumers (via the `PendingApproval` read contract).
- A pending item older than its rule's `escalation_days` → notify next level + original approver's manager.
- Idempotent: once-per-level escalation flag prevents repeat notifications.
- Fires via `core.notifications`; writes no consumer tables.

## UI

- **Kind**: background
- **Page**: none — `EscalateStaleApprovalsCommand` scheduled daily 09:00 on the `notifications` queue.
- **Key interactions**: none (surfaces only as notifications + a badge on the pending queue).
- **Gating**: n/a (system job); resulting notifications gated by recipient's normal access.

## Data

- Owns / writes: escalation flag on its own tracking (in-memory/cache or a lightweight own column) — no consumer-table writes.
- Reads: consumer pending-approval read model; rule escalation_days.
- Cross-domain writes: **only** via `core.notifications` events ([[../../../../security/data-ownership]]).

## Relations

- Consumes: pending-approval state exposed by [[../../requisitions/_module|requisitions]] / [[../../purchase-orders/_module|POs]].
- Feeds: notification events → `core.notifications`.

## Test Checklist

### Unit
- [ ] Staleness: pending action older than `escalation_days` flagged; notify next level + approver's manager

### Feature (Pest)
- [ ] `EscalateStaleApprovalsCommand` notifies once per stale approval (guard flag); never writes consumer tables
- [ ] Tenant isolation: scans per company

### Livewire
- (none -- scheduled command)

## Unknowns

- Business-day vs calendar-day window. **UNVERIFIED** — no company-calendar source.
- Manager lookup source (HR org chart soft dep) when HR inactive → fallback to owner? `*(assumed)*`

## Related

- [[../_module|Approvals]] · [[pending-approvals-queue]] · [[../../../../architecture/queue-jobs]]
