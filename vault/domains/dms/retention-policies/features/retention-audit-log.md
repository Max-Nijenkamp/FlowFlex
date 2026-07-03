---
domain: dms
module: retention-policies
feature: retention-audit-log
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Retention Audit Log

An append-only record of every retention action — archived, soft-deleted, hard-deleted, notified — kept as compliance proof. Read-only.

## Behaviour

1. Every action taken by the [[retention-run|Retention Run]] writes a `dms_retention_log` row: `document_id`, `policy_id`, `action` (`archived` / `soft-deleted` / `hard-deleted` / `notified`), `executed_at`.
2. **Append-only** — rows are never updated or deleted; the log is the compliance ledger.
3. The same `(document_id, action)` pair also serves as the idempotency guard for daily re-runs — an existing row means the action already happened.
4. Users with `dms.retention.view-log` browse it; no one edits it.

## UI

- **Kind**: simple-resource, **read-only** (retention log).
- **Page**: "Retention Log" (`/dms/retention-log`), nav group **Settings**.
- **Columns**: document · policy · action · executed_at.
- **Form**: none — create / edit / delete disabled at the resource level.
- **Filters**: action · policy · date range.
- **Row actions**: view only (link to the document if it still exists).
- **States**: empty ("no retention actions recorded yet") · error (toast).
- **Gating**: `dms.retention.view-log` + `BillingService::hasModule('dms.retention')`.

## Data

- Owns / writes: `dms_retention_log` (this module) — written by the run, displayed read-only here.
- Reads: `dms_retention_policies` (own, for policy names); documents owned by [[../../document-library/_module|dms.library]] for display links.
- Cross-domain writes: none.

## Relations

- Consumes: rows produced by [[retention-run|Retention Run]].
- Feeds: nothing — terminal compliance view.
- Shared entity: documents (owned by `dms.library`), policies (own).

## Test Checklist

### Unit
- [ ] Idempotency guard: existing `(document_id, action)` row means the action is reported as already-performed

### Feature (Pest)
- [ ] Every run action (archived / soft-deleted / hard-deleted / notified) writes exactly one log row with policy + timestamp
- [ ] Log rows cannot be updated or deleted via any exposed path (append-only)
- [ ] Tenant isolation: company A's log never lists company B's documents; `dms.retention.view-log` required

### Livewire
- [ ] Retention log resource is read-only: no create/edit/delete actions rendered; filters by action/policy/date work
- [ ] canAccess(): hidden without `dms.retention.view-log` or with `dms.retention` module inactive

## Unknowns

- Retention period of the log itself (how long compliance proof is kept) — not stated in source.

## Related

- [[../_module|Retention Policies]] · [[retention-run]] · [[retention-policy]] · [[legal-hold]]
- [[../../document-library/_module|Document Library]] · [[../../../../architecture/data-lifecycle]]
