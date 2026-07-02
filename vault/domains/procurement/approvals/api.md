---
domain: procurement
module: approvals
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Approvals — DTOs & Service API

## DTOs (spatie/laravel-data)

### CreateApprovalRuleData
`applies_to` (in:requisition,po), `min_amount_cents`/`max_amount_cents` (min < max; **no overlap** with existing rules at same level/category — "Amount ranges may not overlap."), `category?`, `approver_role` (exists as role), `level` (min:1), `escalation_days`.

### CreateDelegationData
`delegate_id` (≠ self), `start_date`/`end_date` (end ≥ start; no overlap with existing delegation for the delegator).

## Service API (`ApprovalMatrix`, support class)

| Method | Signature | Notes |
|---|---|---|
| `chainFor` | `chainFor(string $type, Money $amount, ?string $category): array` | ordered approver levels; resolves delegations at act time |
| `resolveApprover` | `resolveApprover(string $role, ?string $userId): User` | delegation-aware role→user |

Consumers call `chainFor` on submit; they record the returned levels in their own approval tables.

## Console

| Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `EscalateStaleApprovalsCommand` | notifications | daily 09:00 | once-per-level escalation flag |

## Read API (for consumers / escalation)

- `PendingApproval` read contract: `{entity_type, entity_id, level, approver_id, waiting_since}` — populated by consumers, scanned by escalation. No write access to consumer tables.

## Related

- [[_module]] · [[data-model]] · [[architecture]]
