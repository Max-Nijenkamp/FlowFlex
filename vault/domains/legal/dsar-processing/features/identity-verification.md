---
domain: legal
module: dsar-processing
feature: identity-verification
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Identity Verification

Verify the data subject before any DSAR fulfilment; blocks core.privacy processing until done.

## Behaviour

- Verification checklist + method recorded (email-challenge / document / in-person *(assumed)*).
- On verify, a `verified` action is appended and the gate lifts — core.privacy processing may proceed.
- When this module is active, fulfilment is blocked until verified.

## UI

- **Kind**: custom-page — verification is a guided step inside the DSAR fulfilment workflow, not a plain form.
- **Page**: verification step on `DsarFulfilmentPage` (`/legal/dsar/{id}`).
- **Layout**: subject summary + verification method radio + checklist + notes; a prominent "processing blocked until verified" banner while unverified.
- **Key interactions**: pick method → complete checklist → confirm → gate lifts + `verified` action logged.
- **States**: empty (unverified → blocked banner) · loading (saving) · error (validation) · selected (verified → green, fulfilment unlocked).
- **Gating**: `legal.dsar.verify`.

## Data

- Owns / writes: `legal_dsar_actions` (a `verified` action; notes encrypted).
- Reads: `dsar_requests` (core.privacy) for subject context (read-only).
- Cross-domain writes: none — the gate influences core.privacy via a hook, not a write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `DSARRequestSubmitted` context (via listener) that created the request.
- Feeds: verified state unblocks core.privacy fulfilment.
- Shared entity: `dsar_requests` (owned by core.privacy).

## Unknowns

- `*(assumed)*` verification methods; gate scope when module deactivated mid-request — [[../unknowns]].

## Related

- [[../_module|DSAR Processing]] · [[./data-discovery]] · [[./fulfilment-delegation]]
