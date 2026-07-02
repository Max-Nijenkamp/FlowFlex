---
domain: hr
module: dei-metrics
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# DEI Metrics — API / DTOs & Services

No cross-domain events are fired or consumed. See [[architecture]] for the service internals and [[_module]] for context.

## DTOs

### SubmitDeiAttributesData (self-service, own only)

| Field | Type | Validation |
|---|---|---|
| attributes | array<{dimension, value}> | dimensions in jurisdiction-allowed set; values in dimension option list; consent checkbox required |

## Services & Actions

| Class | Signature | Notes |
|---|---|---|
| `DeiSnapshotService` | `generate(string $period): void` | aggregation pipeline (see [[architecture]]) |
| `SubmitOwnDeiAttributesAction` | `run(SubmitDeiAttributesData $data): void` | own-only; writes consent log |
| `WithdrawDeiConsentAction` | `run(): void` | deletes own attributes + logs withdrawal |

## Events

None. `fires-events: []`, `consumes-events: []`.

## Related

- [[architecture]]
- [[security]]
