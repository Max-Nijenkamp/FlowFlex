---
domain: projects
module: okrs
feature: objectives-key-results
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Objectives & Key Results

Create nested objectives with measurable key results; progress rolls up the hierarchy.

## Behaviour

- Objective: title, owner, quarter/year, optional parent (cycle-checked, depth ≤ 4).
- Key Results: target/current/baseline value + unit; progress = baseline-aware, clamped 0–100.
- Objective progress = average of KRs; cascades to parent objectives.

## UI

- **Kind**: simple-resource (tree-ish list + KR relation manager).
- **Page**: `ObjectiveResource` at `/app/projects/okrs` (nav group OKRs).
- **Layout**: nested/indented objective list; expand → KR relation manager (target/current/baseline/unit + progress bar); parent picker on form.
- **Key interactions**: create objective + KRs; reparent (cycle/depth validated); progress bars reflect roll-up.
- **States**: empty (no objectives → CTA) · loading · error (cycle/depth → "This would create a loop / exceed max depth") · selected (objective expanded).
- **Gating**: `projects.okrs.view-any`; create `create`.

## Data

- Owns / writes: `proj_objectives`, `proj_key_results`.
- Reads: `users` (owner), optional `proj_projects` link.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes / Feeds: nothing.
- Shared entity: `users`, optional `proj_projects`.

## Test Checklist

### Unit
- [ ] KR progress = `(current − baseline) / (target − baseline)` clamped 0–100.
- [ ] Objective progress = average of its KR progress; cascades to the parent objective.

### Feature (Pest)
- [ ] Create objective with a nested parent respecting depth ≤ 4; a reparent that would create a cycle is rejected.
- [ ] Create requires `projects.okrs.create`; company A cannot attach a KR to company B's objective (tenant scope).

### Livewire
- [ ] `ObjectiveResource` denied without `projects.okrs.view-any`; hidden when `projects.okrs` inactive.
- [ ] Cycle / depth violation surfaces an inline validation error ("would create a loop / exceed max depth").

## Unknowns

- Auto-updating KRs from cross-domain metrics — see [[../unknowns]] + [[../../_opportunities]].

## Related

- [[../_module|OKRs]] · [[checkins-dashboard|Check-ins & Dashboard]]
