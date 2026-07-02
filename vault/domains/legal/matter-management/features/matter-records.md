---
domain: legal
module: matter-management
feature: matter-records
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Matter Records

The core matter entity: type, status, internal owner, external counsel, priority and risk, with document association.

## Behaviour

- Types: litigation / advisory / dispute / IP.
- Status machine `open → active → on_hold → closed` ([[../architecture]]).
- Priority + risk level low/medium/high; notes/updates log.
- Documents associated via Media Library (DMS links when active).
- Spend summary per matter shown when [[../../legal-spend/_module|legal.spend]] active (read-only).

## UI

- **Kind**: simple-resource
- **Page**: `MatterResource` — list + create/edit at `/legal/matters`.
- **Layout**: table (title, type, status badge, priority, owner, confidential badge); form grouped Details / Counsel / Classification / Confidentiality; timeline + documents as relation tabs; spend summary panel (soft-dep).
- **Key interactions**: filter type/status/priority; status transition actions (respect machine); close action; toggle confidential + edit access list.
- **States**: empty ("Open your first matter" CTA) · loading (skeleton) · error (validation / invalid transition) · selected (row → view, confidential badge if restricted).
- **Gating**: view `legal.matters.view-any` **and** confidentiality gate; create/edit `legal.matters.create`/`.update`; close `legal.matters.close`.

## Data

- Owns / writes: `legal_matters`.
- Reads: `legal.spend` spend summary; `dms.library` / `legal.contracts` links; `users` for owner/counsel (all read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: matter list read by legal.spend (expenses attach to matters).
- Shared entity: `users` (platform).

## Test Checklist

### Unit
- [ ] Matter `type` accepts only the allowed set (litigation/advisory/dispute/IP)
- [ ] Priority + risk level accept only low/medium/high
- [ ] Invalid status transition (e.g. `closed → active`) rejected by the state machine

### Feature (Pest)
- [ ] Create matter persists owner, external counsel free-text, classification
- [ ] Close action drives `active → closed` and sets `closed_at`
- [ ] Company A cannot read/list company B matters (CompanyScope)
- [ ] Spend-summary panel present only when `legal.spend` active; hidden otherwise

### Livewire
- [ ] `MatterResource` form validates required fields and transition guards
- [ ] Status/close header actions gate on `change-status` / `close` permissions
- [ ] `canAccess()` false when module inactive or permission missing

## Unknowns

- External counsel is free-text — no counsel entity ([[../unknowns]]).

## Related

- [[../_module|Matter Management]] · [[./matter-timeline]] · [[./confidential-access]]
