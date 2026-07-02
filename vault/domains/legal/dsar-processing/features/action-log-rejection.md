---
domain: legal
module: dsar-processing
feature: action-log-rejection
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Action Log & Rejection

Append-only per-domain action trail plus documented rejection (e.g. legal-hold exemption).

## Behaviour

- Every DSAR step recorded in `legal_dsar_actions` (verified / discovery-run / export-delivered / erasure-run / rectified / rejected).
- Append-only — compliance proof, never edited or purged ([[../../../architecture/data-lifecycle]]).
- Rejection requires a documented reason in `notes` (encrypted); `rectified` likewise.

## UI

- **Kind**: simple-resource — the action trail is an infolist/relation table on the extended `DsarRequestResource`, with a reject action; not a standalone screen.
- **Page**: action trail + reject action on `DsarRequestResource` (extended) (`/legal/dsar`).
- **Layout**: deadline-sorted request list; per-request timeline of actions (action, domain, performer, timestamp); reject action with required reason.
- **Key interactions**: view trail; reject → required notes → `rejected` action logged; record `rectified` with notes.
- **States**: empty ("No actions yet") · loading (skeleton) · error ("A reason is required to reject") · selected (request expanded, trail shown).
- **Gating**: view `legal.dsar.view-any`; reject `legal.dsar.reject`.

## Data

- Owns / writes: `legal_dsar_actions` (append-only; `notes` encrypted).
- Reads: `dsar_requests` (core.privacy) for the list + deadlines (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `DSARRequestSubmitted` (via listener) seeds the initial review action.
- Feeds: the action log is the audit evidence for the whole DSAR.
- Shared entity: `dsar_requests` (owned by core.privacy).

## Test Checklist

### Unit
- [ ] `RecordDsarActionData` rejects `rejected`/`rectified` actions without notes

### Feature (Pest)
- [ ] Every workflow step appends its action row; rows are never updated or deleted (append-only assertion)
- [ ] `DSARRequestSubmitted` listener seeds the initial review action (queued, `WithCompanyContext`)
- [ ] Rejection stores encrypted notes; plaintext absent from DB dump

### Livewire
- [ ] Action timeline renders deadline-sorted; reject without reason shows "A reason is required to reject"
- [ ] Reject denied without `legal.dsar.reject`

## Unknowns

- `*(assumed)*` rectification/portability documented as manual actions — [[../unknowns]].

## Related

- [[../_module|DSAR Processing]] · [[./fulfilment-delegation]] · [[../security]]
