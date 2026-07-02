---
domain: hr
module: dei-metrics
feature: dei-attributes-encrypted
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Encrypted DEI attributes

## Purpose

Store self-declared diversity attributes such that individual values are never readable via SQL, never indexed, and never rendered anywhere.

## Behavior

- `hr_dei_attributes.value` is encrypted at rest (`text` column).
- Never indexed, never SQL-filtered. Decryption happens transiently only inside the snapshot job, then individuals are discarded.
- Unique `(employee_id, dimension)` — one value per dimension per employee.

## Tables / Permissions

- Owns `hr_dei_attributes`. Encrypted field: `hr_dei_attributes.value`.
- No permission exposes individual values — not even `view-any`.

## UI

- **Kind**: background (cross-cutting data concern — no standalone screen)
- **Page**: none — this is an at-rest storage/encryption concern, not a rendered view; values are written by [[self-declaration]] and read only transiently inside the snapshot job
- **Layout**: n/a — no UI ever renders an individual `value`
- **Key interactions**: none directly; the encrypted column is populated by the self-declaration action and decrypted only inside `GenerateDeiSnapshotsCommand`
- **States**: n/a (no screen) — the only observable surface is the absence of individual values anywhere in the UI
- **Gating**: no permission exposes individual values at all — not even `view-any`; encryption + no-index/no-filter enforced at the model/column level

## Data

- Owns / writes: `hr_dei_attributes` (encrypted `value` column; unique `(employee_id, dimension)`)
- Reads: nothing at request time; decrypted transiently only inside the quarterly snapshot job
- Cross-domain writes: none ([[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none outbound — decrypted set is consumed only in-process by [[anonymized-snapshots]], then discarded
- Shared entity: `hr_employees` (FK owner of each attribute row)

## Related

- [[../_module]]
- [[../security]]
- [[../../../../architecture/patterns/encryption]]
