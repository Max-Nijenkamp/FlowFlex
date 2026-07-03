---
domain: it
module: access-provisioning
feature: access-grants
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Access Grants

The record of who has (or should have) access to which system: employee → system → access level with
granted/revoked dates and status. IT works this list to complete provisioning and revocation.

- Simple Filament resource over `it_access_grants` ([[../../../../architecture/patterns/feature-ui-spec]]).
- Status lifecycle: `pending → granted → revoke-flagged → revoked`.
- Grant / revoke row actions call `AccessService::grant` / `::revoke` (stamps + audit).

## UI

- **Kind**: simple-resource — CRUD + status tabs over `it_access_grants`.
- **Page**: `AccessGrantResource` at `/it/access-grants`.
- **Layout**: tabs **Pending** (status = pending) and **Flagged** (status = revoke-flagged); table columns employee · system · access level · status · granted_at / revoked_at; row actions **Grant** and **Revoke**.
- **Key interactions**: Grant → `AccessService::grant` (stamps `granted_at`/`granted_by`); Revoke → `AccessService::revoke` (stamps `revoked_at`/`revoked_by`); create grant validates no active grant exists — **a duplicate active `(employee_id, system_id)` is rejected**.
- **States**: empty (no grants → CTA) · loading (table skeleton) · error (duplicate-active grant → validation toast) · selected (grant row highlighted).
- **Gating**: view `it.access.view-any`; grant `it.access.grant`; revoke `it.access.revoke`.

## Data

- Owns / writes: `it_access_grants` only.
- Reads: `it_systems` (system names); employee reference from hr.profiles.
- Cross-domain writes: none — cross-domain effects flow through events only, never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: grants are created by [[provisioning-on-hire]] and flagged by [[deprovisioning-on-offboard]].
- Feeds: grant state into the [[access-review-matrix]].
- Shared entity: employee owned by hr.profiles (read only); system owned by [[system-catalogue]].

## Test Checklist

### Unit
- [ ] Access level must be in the `admin / user / read` set

### Feature (Pest)
- [ ] Grant stamps `granted_at` / `granted_by` + writes audit; revoke stamps `revoked_at` / `revoked_by`
- [ ] Duplicate active `(employee_id, system_id)` grant rejected (concurrent double-grant blocked via row lock)
- [ ] Grants tenant-scoped: company A cannot grant / revoke company B grants

### Livewire
- [ ] Pending / Flagged tabs filter by status; Grant / Revoke row actions gated by `it.access.grant` / `it.access.revoke`

## Unknowns

- Access-level set (`admin / user / read`) — `*(assumed set)*`.
- Active-grant uniqueness via partial unique index on non-revoked rows — `*(assumed)*`.

## Related

- [[../_module|Access Provisioning]] · [[system-catalogue]] · [[provisioning-on-hire]] · [[deprovisioning-on-offboard]] · [[../security|security]]
