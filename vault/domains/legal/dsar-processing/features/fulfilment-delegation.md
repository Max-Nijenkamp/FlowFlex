---
domain: legal
module: dsar-processing
feature: fulfilment-delegation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Fulfilment Delegation

Trigger export/erasure — but delegate the actual work to core.privacy's jobs. No duplicate erasure logic here.

## Behaviour

- Access export / erasure cascade run via core.privacy PersonalDataRegistry jobs *(assumed)*.
- legal.dsar records `export-delivered` / `erasure-run` actions once the delegated job completes.
- Deadline view (30-day) uses core.privacy's `due_at`.

## UI

- **Kind**: custom-page
- **Page**: fulfilment section on `DsarFulfilmentPage` (`/legal/dsar/{id}`).
- **Layout**: discovery table + "trigger export" / "trigger erasure" buttons (disabled until verified); status of the delegated job; deadline countdown from `due_at`.
- **Key interactions**: trigger export/erasure → dispatch core.privacy job → poll status → log action on completion.
- **States**: empty (verified, nothing triggered) · loading (job running) · error (job failed → retry) · selected (job complete → action logged).
- **Gating**: `legal.dsar.process` (blocked until identity verified).

## Data

- Owns / writes: `legal_dsar_actions` (`export-delivered` / `erasure-run`).
- Reads: `dsar_requests.due_at` + job status (core.privacy), read-only.
- Cross-domain writes: none — export/erasure execute in core.privacy's own jobs against its own tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: core.privacy job completion (to log the action).
- Feeds: nothing downstream — terminal fulfilment.
- Shared entity: `dsar_requests` + PersonalDataRegistry (owned by core.privacy).

## Test Checklist

### Unit
- [ ] Deadline countdown derives from core.privacy `due_at`

### Feature (Pest)
- [ ] Trigger export dispatches the core.privacy job (faked) — no local export logic runs
- [ ] Job completion appends `export-delivered` / `erasure-run` action exactly once
- [ ] Trigger blocked while identity unverified

### Livewire
- [ ] Export/erasure buttons disabled until verified; job status renders; failed job shows retry
- [ ] Denied without `legal.dsar.process`

## Unknowns

- `*(assumed)*` erasure runs via PersonalDataRegistry jobs (v1 `DSARErasureRequested` event dropped) — [[../unknowns]].

## Related

- [[../_module|DSAR Processing]] · [[./data-discovery]] · [[./action-log-rejection]]
