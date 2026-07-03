---
domain: procurement
module: approvals
feature: delegation
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Approver Delegation

An approver going away delegates their approval authority to a colleague for a date range. Resolved at act time.

## Behaviour

- Delegation: delegator, delegate (≠ delegator), start_date, end_date (end ≥ start).
- Overlapping delegations for the same delegator are rejected.
- At approval act time, `resolveApprover` substitutes the delegate if today ∈ [start, end].
- Delegate must still satisfy the approver role check via the delegation (they act "as" the delegator's authority).

## UI

- **Kind**: simple-resource
- **Page**: "My Delegations" (`/operations` → Procurement → Settings → Delegations)
- **Layout**: table of own delegations — delegate, date range, active badge.
- **Key interactions**: create delegation (delegate picker, date range); overlap validation inline; revoke (delete).
- **States**: empty ("No active delegations") · loading (skeleton) · error (toast) · active (green badge for in-window rows).
- **Gating**: `procurement.approvals.delegate-own` (delegator forced to self); managing others' delegations requires `procurement.approvals.manage-rules`.

## Data

- Owns / writes: `proc_approval_delegations`.
- Reads: user directory (`core` users) for the delegate picker.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Feeds: `ApprovalMatrix::resolveApprover` consulted by requisitions/PO approval acts.
- Shared entity: users (core).

## Test Checklist

### Unit
- [ ] Delegation active iff today within date range; resolved at act time, not chain-build time

### Feature (Pest)
- [ ] Delegation added after submission still routes the act to the delegate
- [ ] Self-delegation and overlapping duplicate delegation rejected *(assumed)*
- [ ] Tenant isolation on delegations

### Livewire
- [ ] Delegation form validates range + delegate; hidden without permission/module

## Unknowns

- Delegation chaining (delegate also away) out of scope v1? `*(assumed: single hop)*`

## Related

- [[../_module|Approvals]] · [[approval-matrix]] · [[../api]]
