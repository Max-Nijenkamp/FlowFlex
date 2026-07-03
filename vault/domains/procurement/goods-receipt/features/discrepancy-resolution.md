---
domain: procurement
module: goods-receipt
feature: discrepancy-resolution
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Discrepancy Resolution

Human workflow to clear a flagged match: override-approve (pay despite variance) or reject-bill, always with notes and an audit trail.

## Behaviour

- `resolve(ResolveMatchData)` with action `override-approve` or `reject-bill`; notes required.
- `override-approve` → `overridden`, `approved_for_payment = true`; requires `procurement.goods-receipt.override`.
- `reject-bill` → holds the bill; fires `ThreeWayMatchResolved` so Finance's listener acts.
- Every resolution audited (who, why, variance at the time).

## UI

- **Kind**: custom-page (actions within the [[match-evaluation|3-Way Match board]]).
- **Page**: resolution modal on the compare pane.
- **Layout**: variance summary + notes field + override/reject buttons.
- **Key interactions**: choose action → notes required → confirm → optimistic status change + toast.
- **States**: empty (no discrepancy → no action) · loading (submitting) · error (toast, unchanged) · selected (resolution modal open).
- **Gating**: `procurement.goods-receipt.resolve`; override additionally requires `procurement.goods-receipt.override`.

## Data

- Owns / writes: `proc_three_way_matches` (status, notes, approved_for_payment).
- Reads: current match + linked docs.
- Cross-domain writes: none; effects flow via `ThreeWayMatchResolved` to Finance ([[../../../../security/data-ownership]]).

## Relations

- Consumes: match state from [[match-evaluation]].
- Feeds: `ThreeWayMatchResolved` → finance AP (release/hold).

## Test Checklist

### Unit
- [ ] Resolution requires notes; override flips `approved_for_payment`, reject does not

### Feature (Pest)
- [ ] Override is permission-gated (dedicated verb) + audited + fires `ThreeWayMatchResolved` once under race (locked row)
- [ ] Tenant isolation: resolving another company's match rejected

### Livewire
- [ ] Resolve actions on flagged rows only; notes required in the modal; hidden without the override permission

## Unknowns

- Segregation of duties (overrider ≠ bill creator). `*(assumed)*` ([[../decisions]]).

## Related

- [[../_module|3-Way Match]] · [[match-evaluation]] · [[payment-gate]]
