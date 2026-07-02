---
domain: procurement
module: goods-receipt
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# 3-Way Match — DTOs, Service API & Events

## DTOs

### ResolveMatchData
`match_id`, `action` (in:override-approve,reject-bill), `notes` (required).

### MatchData (output)
`match_status`, `variance_cents`, `approved_for_payment`, po/grn/bill refs.

## Service API

| Method | Signature | Notes |
|---|---|---|
| `ThreeWayMatchService::evaluate` | `evaluate(string $billId): MatchData` | finds PO/GRN via bill links, computes variances, sets status; auto-approves within tolerance |
| `ThreeWayMatchService::resolve` | `resolve(ResolveMatchData): MatchData` | override/reject path, audited; fires `ThreeWayMatchResolved` |

## AP gate hook

- On `ApService::approveBill`: when procurement active + bill is PO-linked, require `approved_for_payment`; otherwise raise `MatchFailedException`. This hook **reads** match state — it does not write AP tables.

## Events fired

| Event | Payload | Consumed by |
|---|---|---|
| `ThreeWayMatchResolved` | `company_id` (scalar), `bill_id`, `match_status`, `approved_for_payment` | finance AP (release/hold payment) via its own listener |

## Related

- [[_module]] · [[data-model]] · [[architecture]] · [[../../finance/accounts-payable/_module]]
